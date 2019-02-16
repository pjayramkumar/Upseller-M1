<?php 
class Upseller_ClouldsearchAdvance_Model_Observer
{ 


	public function productSaveAfter($observer)
    {
    	$databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();

        foreach($storeIds as $storeId){

            $_helper=Mage::helper('upseller_clouldsearch');

            if($_helper->IsActive($storeId['store_id'])){

                $objectType="products";

                $productId=$observer->getProduct()->getId();

                $object=$databaseObject->getProductDataById($productId,$storeId['store_id']);
				
				$productModel = Mage::getModel('catalog/product')->load($productId);
				$productExistInStoreIds = $productModel->getStoreIds();
				
				$synchronization=Mage::getModel('upseller_clouldsearch/synchronization');
                
				if(in_array($storeId['store_id'], $productExistInStoreIds)){
					$synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"put");
				}else{
					$synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"delete");
				}
            }
        }
    }

	public function productWebsiteChangeAfter($observer)
    {
    	$databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();
		
		$productIds = $observer->getProducts(); 
		
		foreach($productIds as $key => $productId){
			foreach($storeIds as $storeId){

	            $_helper=Mage::helper('upseller_clouldsearch');

	            if($_helper->IsActive($storeId['store_id'])){

	                $objectType="products";

	                $object=$databaseObject->getProductDataById($productId,$storeId['store_id']);
					
					$productModel = Mage::getModel('catalog/product')->load($productId);
					$productExistInStoreIds = $productModel->getStoreIds();
					
					$synchronization=Mage::getModel('upseller_clouldsearch/synchronization');
	                
					if(in_array($storeId['store_id'], $productExistInStoreIds)){
						$synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"put");
					}else{
						$synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"delete");
					}
	            }
	        }
		}
		
    }


    public function categoriesSaveAfter($observer)
    {
		$databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();

        foreach($storeIds as $storeId){

            $_helper=Mage::helper('upseller_clouldsearch');

            if($_helper->IsActive($storeId['store_id'])){

                $objectType="categories";

                $categoryId=$observer->getEvent()->getCategory()->getId();

                $object=$databaseObject->getCategoryDataById($categoryId,$storeId['store_id']);

                $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');
                
                $synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"put");

            }

        }

    }

    public function catalogruleRuleSaveAfter($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){

            Mage::getSingleton("core/session")->addNotice("You must need to synchronize your data to Cloudsearch."); 
        }
    }

}
?>    