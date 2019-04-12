<?php
class Upseller_Clouldsearch_Helper_Data extends Mage_Core_Helper_Abstract{

	protected $_isDevelopmentMode=true;

	public $_cms="magento";

	public $_version="";

	public function __construct(){

		$this->_version=Mage::getVersion();
	}

	public function getProtocol(){

		return "https";
	}

	public function getSearchDomain($storeId){

		$searchdomain=Mage::getStoreConfig('upseller_clouldsearch/settings/searchdomain',$storeId);
		return $searchdomain;

	}	

	public function getSearchUrl($storeId){

		$subdomain=$this->getSubdomain($storeId);
		$clusterkey=$this->getClusterKey($storeId);
		$searchurl=$this->getProtocol().'://'.$clusterkey.'.'.$subdomain.'.'.$this->getSearchDomain($storeId).'/';
		return $searchurl;
	
	}

	public function getCloudsearchUid($storeId=null){

		$cloudsearchUid=Mage::getStoreConfig('upseller_clouldsearch/settings/apiuid',$storeId);
		return $cloudsearchUid;
	}

	public function getCloudsearchKey($storeId=null){

		$cloudsearchKey=Mage::getStoreConfig('upseller_clouldsearch/settings/apikey',$storeId);
		return $cloudsearchKey;
	}

	public function IsActive($storeId=null){

		$active=Mage::getStoreConfig('upseller_clouldsearch/settings/active',$storeId);
		return $active;
	}

	public function IsQuickSearchActive($storeId){

		$activeQuicksearch=Mage::getStoreConfig('upseller_clouldsearch/settings/active_quicksearch',$storeId);
		return $activeQuicksearch;
	}

	public function IsAdvanceSearchActive($storeId){

		$activeQuicksearch=Mage::getStoreConfig('upseller_clouldsearch/settings/active_advance',$storeId);
		return $activeQuicksearch;
	}

	public function IsCatalogSearchActive($storeId){

		$activeQuicksearch=Mage::getStoreConfig('upseller_clouldsearch/settings/active_catalogsearch',$storeId);
		return $activeQuicksearch;
	}

	public function getStoreCurrencyCode($storeId){

		$store=Mage::getModel('core/store')->load($storeId);
        $currentCurrencyCode = $store->getCurrentCurrencyCode(); 

        return $currentCurrencyCode;
	}

	public function IsPriceIncludingTax($storeId){
		
		$priceIncludesTax=Mage::getStoreConfig('tax/calculation/price_includes_tax',$storeId);
		return $priceIncludesTax;
	}

	public function priceDisplayType($storeId){
		
		$taxDisplayType=Mage::getStoreConfig('tax/display/type',$storeId);
		$returnVal='incl';
		if($taxDisplayType==1){
			$returnVal='excl';
		}elseif($taxDisplayType==2){
			$returnVal='incl';
		}elseif($taxDisplayType==3){
			$returnVal='both';
		}
		return $returnVal;
	}

	public function getSubdomain($storeId){

		$subdomain=Mage::getStoreConfig('upseller_clouldsearch/settings/subdomain',$storeId);
		return $subdomain;
	}

	public function getClusterKey($storeId){

		$clusterkey=Mage::getStoreConfig('upseller_clouldsearch/settings/cluster_key',$storeId);
		return $clusterkey;
	}

}
?>