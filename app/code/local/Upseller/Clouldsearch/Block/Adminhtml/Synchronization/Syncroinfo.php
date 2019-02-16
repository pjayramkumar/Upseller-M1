<?php
class Upseller_Clouldsearch_Block_Adminhtml_Synchronization_Syncroinfo extends Mage_Adminhtml_Block_Template
{
   
	public function getAttributeSyncroInfo(){

		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();

		$attributes=$cloudseachSession['attributes'];
		$attributesCategoriesFinished=$attributes['categories']['attributes_finished'];
		$attributesCategoriesTotal=$attributes['categories']['attributes_total'];
		$attributesCategoriesBatch=$attributes['categories']['attributes_batch'];
		$attributesCategoriesCurrentChunk=$attributes['categories']['attributes_current_chunk'];

		$attributesProductsFinished=$attributes['products']['attributes_finished'];
		$attributesProductsTotal=$attributes['products']['attributes_total'];
		$attributesProductsBatch=$attributes['products']['attributes_batch'];
		$attributesProductsCurrentChunk=$attributes['products']['attributes_current_chunk'];

		$attributesClass="";
		$totalDone=(($attributesCategoriesCurrentChunk*$attributesCategoriesBatch)+($attributesProductsCurrentChunk*$attributesProductsBatch));
		if($attributesCategoriesFinished && $attributesProductsFinished){
			$attributesClass="finish";
			$totalDone=$attributesCategoriesTotal+$attributesProductsTotal;
		}elseif(($attributesCategoriesFinished==0 || $attributesProductsFinished==0) || ($attributesCategoriesCurrentChunk>0 || $attributesProductsCurrentChunk>0)){
			$attributesClass="continue";
		}

		$returnArray=[];

		$returnArray['attributesClass']=$attributesClass;
		$returnArray['totalDone']=$totalDone;
		$returnArray['total']=$attributesCategoriesTotal+$attributesProductsTotal;

		return $returnArray;

	}


	public function getCatalogSyncroInfo(){

		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();
		
		

		$database=Mage::getModel('upseller_clouldsearch/database');
		$stores=$database->getStores();

		$categories=$cloudseachSession['categories'];
		$products=$cloudseachSession['products'];

		$categoryResult=[];
		$categoriesBatch=0;
		
		$productResult=[];
		$productBatch=0;
		

		foreach($stores as $store){

			$categoriesBatch=$categories[$store['code']]['categories_batch'];
			$categoryResult["finish"][]=$categories[$store['code']]['categories_finished'];
			$categoryResult["total"][]=$categories[$store['code']]['categories_total'];
			$categoryResult["total_done"][]=$categories[$store['code']]['categories_current_chunk']*$categories[$store['code']]['categories_batch'];

			$productBatch=$products[$store['code']]['products_batch'];
			$productResult["finish"][]=$products[$store['code']]['products_finished'];
			$productResult["total"][]=$products[$store['code']]['products_total'];
			$productResult["total_done"][]=$products[$store['code']]['products_current_chunk']*$products[$store['code']]['products_batch'];

		}

		$categoryClass="";
		$categoryIsDone=array_sum($categoryResult["finish"])/count($categoryResult["finish"]);
		$categorydone=array_sum($categoryResult["total_done"]);
		$categoryTotal=array_sum($categoryResult["total"]);
		if($categoryIsDone==1){
			$categoryClass="finish"; 
			$categorydone=$categoryTotal;
		}elseif($categoryIsDone==0 && $categorydone==0){
			$categoryClass="";
		}else{
			$categoryClass="continue";
		}


		$productClass="";
		$productIsDone=array_sum($productResult["finish"])/count($productResult["finish"]);
		$productdone=array_sum($productResult["total_done"]);
		$productTotal=array_sum($productResult["total"]);
		if($productIsDone==1){
			$productClass="finish"; 
			$productdone=$productTotal;
		}elseif($productIsDone==0 && $productdone==0){
			$productClass="";
		}else{
			$productClass="continue";
		}


		$returnArray=[];

		$returnArray['categoryClass']=$categoryClass;
		$returnArray['categorydone']=$categorydone;
		$returnArray['categoryTotal']=$categoryTotal;

		$returnArray['productClass']=$productClass;
		$returnArray['productdone']=$productdone;
		$returnArray['productTotal']=$productTotal;

		return $returnArray;





	}



}
?>