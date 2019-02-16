<?php
class Upseller_Clouldsearch_Block_Cloudsearch_Script extends  Mage_Core_Block_Template{
	
	public function __construct(){
		
	}

	public function getStoreId(){

		$storeId=Mage::app()->getStore()->getStoreId();
		return $storeId;
	}

	public function getFilters(){

		$filters = [];

		$storeId=$this->getStoreId();

		$_helper=Mage::helper('upseller_clouldsearch');

		if(Mage::registry('current_category') && $_helper->IsAdvanceSearchActive($storeId)){

			$databaseObject=Mage::getModel('upseller_clouldsearch/database');
			$attributeName=$databaseObject->getCategoryNameAttribute();

			$category = Mage::registry('current_category');

			
			$path = '';
			$level = '';

			if ($category && $category->getDisplayMode() !== 'PAGE') {
		        $category->getUrlInstance()->setStore($storeId);

		        $level = -1;
		        $pathArray = []; 
		        $_path = "";
		        foreach ($category->getPathIds() as $treeCategoryId) {
		            
		            $parentId=$databaseObject->getCategoryParentId($treeCategoryId);
		            //if($parentId!=0){
			            $_path=$databaseObject->getCategoryNameById($treeCategoryId,$attributeName,$storeId);
			            $pathArray [] = $_path;

			            if ($_path) {
			                $level++;
			            }
			        //}    
		        }

		        //Zend_Debug::dump($pathArray);
		        unset($pathArray[0]);
		        unset($pathArray[1]);
		       // Zend_Debug::dump($pathArray);
		        //exit;
		        $path = implode(" /// ",$pathArray);
			

		        $filters['category_ids.level'.$level]=$path;

		    }

		}

		return $filters;

	}


	public function getJsonConfig() {
	
		$storeId=$this->getStoreId();

		$_helper=Mage::helper('upseller_clouldsearch');

		$locale=explode("_",Mage::getStoreConfig('general/locale/code', $storeId));

		$query = '';

		$upsellerCls=array();
		$upsellerCls['apiuid']=$_helper->getCloudsearchUid($storeId);
		$upsellerCls['apikey']=$_helper->getCloudsearchKey($storeId);
		$upsellerCls['quicksearch']=$_helper->IsQuickSearchActive($storeId);
		$upsellerCls['advancesearch']=$_helper->IsAdvanceSearchActive($storeId);
		$upsellerCls['catalogsearch']=$_helper->IsCatalogSearchActive($storeId);
		$upsellerCls['searchurl']=$_helper->getSearchUrl($storeId);
		$upsellerCls['cms']=$_helper->_cms;
		$upsellerCls['cmsversion']=$_helper->_version;
		$upsellerCls['storeid']=$storeId;
		$upsellerCls['langcode']=strtolower($locale[0]);
		$upsellerCls['currency']=$_helper->getStoreCurrencyCode($storeId);
		$upsellerCls['is_price_including_tax']=$_helper->IsPriceIncludingTax($storeId);
		$upsellerCls['request']=array(
				'q'=>html_entity_decode($query),
				'filters' => $this->getFilters(),
				'page' => 1,
			);

		return Mage::helper ('core')->jsonEncode($upsellerCls);
	
	}
	
	public function getCustomerId(){

		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer =Mage::getSingleton('customer/session');
			$customerId=$customer->getId();
		}else{
			$customerId=0;
		}

		return $customerId;
	}
	
	public function getCurrentDate(){

		return date("Y-m-d");
	}

	public function getFormKey(){

		return Mage::getSingleton('core/session')->getFormKey();
	}

	public function getTemplateVariableJsonConfig(){

		$storeId=$this->getStoreId();

		$_helper=Mage::helper('upseller_clouldsearch');


		$categoryDisplayMode="PAGE";
		if(Mage::registry('current_category') && $_helper->IsAdvanceSearchActive($storeId)){
			$category = Mage::registry('current_category');
			$categoryDisplayMode=$category->getDisplayMode();
		}	


		$confg=[];
		$confg['form_key']=$this->getFormKey();
		$confg['current_customer']=$this->getCustomerId();
		$confg['current_date']=$this->getCurrentDate();
		$confg['currency']=$_helper->getStoreCurrencyCode($storeId);
		$confg['is_price_including_tax']=$_helper->IsPriceIncludingTax($storeId);
		$confg['tax_display_type']=$_helper->priceDisplayType($storeId);
		$confg['category_display_mode']=$categoryDisplayMode;
		return Mage::helper ('core')->jsonEncode($confg);

	}



}