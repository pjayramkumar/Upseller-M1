<?php

class Upseller_Clouldsearch_Adminhtml_ApiController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction(){

		$this->loadLayout();
        $this->renderLayout();
		
	}

	public function syncrodataAction(){

		$post=$this->getRequest()->getPost();

		$syncrodata=Mage::getModel('upseller_clouldsearch/syncrodata');

		$result=$syncrodata->syncroinit($post);

	}

}

?>