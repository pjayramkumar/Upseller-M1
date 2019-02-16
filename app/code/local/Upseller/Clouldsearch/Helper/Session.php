<?php
class Upseller_Clouldsearch_Helper_Session extends Mage_Core_Helper_Abstract{

	public function getCloudSearchSession(){

		$cloudSearchSyncro =Mage::getSingleton('core/session')->getCloudSearchSyncro();

		return $cloudSearchSyncro;

	}

	public function setCloudSearchSession($array){

		Mage::getSingleton('core/session')->setCloudSearchSyncro($array);

	}

	public function isFinished(){

		$cloudseachSession=$this->getCloudSearchSession();

		$attributes=$cloudseachSession['attributes'];
		$attributesCategoriesFinished=$attributes['categories']['attributes_finished'];
		$attributesProductsFinished=$attributes['products']['attributes_finished'];

		$database=Mage::getModel('upseller_clouldsearch/database');
		$stores=$database->getStores();

		$categories=$cloudseachSession['categories'];
		$products=$cloudseachSession['products'];
		$categoryResult=[];
		$productResult=[];

		foreach($stores as $store){

			$categoryResult["finish"][]=$categories[$store['code']]['categories_finished'];
			$productResult["finish"][]=$products[$store['code']]['products_finished'];

		}

		$categoryIsDone=array_sum($categoryResult["finish"])/count($categoryResult["finish"]);
		$productIsDone=array_sum($productResult["finish"])/count($productResult["finish"]);

		if($categoryIsDone==1 && $productIsDone==1 && $attributesCategoriesFinished==true && $attributesProductsFinished==true){
			Mage::getSingleton('core/session')->setCloudSearchSyncro(array());
			return true;
		}else{
			return false;
		}


	}
	

}
?>