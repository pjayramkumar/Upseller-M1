<?php

class Upseller_Clouldsearch_Adminhtml_SynchronizationController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction(){

		$this->loadLayout();
		$this->_title($this->__("Cloudsearch Synchronization"));
        $this->renderLayout();
		
	}

	public function initAction(){

		$_helper=Mage::helper('upseller_clouldsearch');

		$post=Mage::app()->getRequest()->getParams();

		$apiuid=$_helper->getCloudsearchUid($post['store']);
		$apikey=$_helper->getCloudsearchKey($post['store']);
		if($apiuid!="" || $apikey!=""){

			

			$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();

			

			$isCategories=false;
			$isProducts=false;

			$synchronizationArray=[];

			
			if(isset($post['products'])){
				$synchronizationArray['is_products']=true;
			}
			if(isset($post['categories'])){
				$synchronizationArray['is_categories']=true; 
			}

			if(isset($post['store'])){
				$synchronizationArray['store']=$post['store']; 
			}

			$synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

			$return=$synchronization->initialization($synchronizationArray);

			
			
			$this->__ResponceUrl("",$return);
			

		}else{

			$this->__ResponceUrl(Mage::Helper('upseller_clouldsearch')->__("Configuration is not Setup yet."),false);
		}
			
	}

	public function continueAction(){

		$post=Mage::app()->getRequest()->getParams();
		if(isset($post['products'])){
			$synchronizationArray['is_products']=true;
		}
		if(isset($post['categories'])){
			$synchronizationArray['is_categories']=true; 
		}

		if(isset($post['store'])){
			$synchronizationArray['store']=$post['store']; 
		}

		$synchronization=Mage::getModel('upseller_clouldsearch/synchronization');
		$return=$synchronization->continueSyn($synchronizationArray);
			

		$syncronizationSuccess=Mage::getSingleton('core/session')->getSyncronizationSuccess();

		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();

		
		if($syncronizationSuccess){
			$this->__ResponceUrl(Mage::Helper('upseller_clouldsearch')->__("Completed."),$return, true);
		}else{
			$this->__ResponceUrl(Mage::Helper('upseller_clouldsearch')->__("Something Went Wrong , Logout and login again."),false);
		}

	}

	protected function __ResponceUrl($error_message,$return, $syncStatus = false){

		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();

		$layout = Mage::app()->getLayout();
		$update = $layout->getUpdate();
		$update->load('clouldsearch_synchronization_infomation');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();

		$isFinished=Mage::Helper('upseller_clouldsearch/session')->isFinished();


		$returnArray['error']=true;
		$returnArray['finish']=$isFinished;
		$returnArray['error_message']=$error_message;
		$returnArray['loading_html']=$output;
		if($return==true){
			$returnArray['error']=false;
			$returnArray['finish']=$isFinished;
			$returnArray['error_message']=$error_message;
			$returnArray['loading_html']=$output;
		}
		
		if($syncStatus && !$return){
			Mage::getSingleton('core/session')->unsCloudSearchSyncro();
		}
		
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($returnArray));
	}

	/*public function syncrodataAction(){

		$post=$this->getRequest()->getPost();

		$syncrodata=Mage::getModel('upseller_clouldsearch/syncrodata');

		$result=$syncrodata->syncroinit($post);

	}*/

}

?>