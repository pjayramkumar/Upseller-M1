<?php 
class Upseller_Clouldsearch_Model_Observer
{ 

    public function addJsCss($observer){

        $storeId = Mage::app()->getStore()->getStoreId(); 

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive($storeId)){

            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addUpdate('<reference name="head">
                  <block type="core/text" name="csastyle">
                      <action method="setText">
                        <text>
                           <![CDATA[<link rel="stylesheet" type="text/css" href="'.$_helper->getProtocol().'://'.$_helper->getCloudsearchUid($storeId).'.'.$_helper->getSubdomain($storeId).'.'.$_helper->getSearchDomain($storeId).'/csa/cloudesearchauto.css">]]>
                        </text>
                      </action>
                  </block>
                  <block type="core/text" name="csascript">
                      <action method="setText">
                        <text>
                           <![CDATA[<script type="text/javascript" src="'.$_helper->getProtocol().'://'.$_helper->getCloudsearchUid($storeId).'.'.$_helper->getSubdomain($storeId).'.'.$_helper->getSearchDomain($storeId).'/csa/cloudesearchauto.js"></script>]]>
                        </text>
                      </action>
                   </block>
                   <block type="core/text" name="csasalectoscript">
                      <action method="setText">
                        <text>
                           <![CDATA[<script type="text/javascript" src="'.$_helper->getProtocol().'://'.$_helper->getCloudsearchUid($storeId).'.'.$_helper->getSubdomain($storeId).'.'.$_helper->getSearchDomain($storeId).'/csa/salecto.js"></script>]]>
                        </text>
                      </action>
                   </block>
            </reference>');
            $layout->generateXml();

        }    

        return $this;
    }

    /*public function catalogCategorySaveAfter($observer){

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

    public function catalogProductSaveAfter($observer){

        $databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();

        foreach($storeIds as $storeId){

            $_helper=Mage::helper('upseller_clouldsearch');

            if($_helper->IsActive($storeId['store_id'])){

                $objectType="products";

                $productId=$observer->getProduct()->getId();

                $object=$databaseObject->getProductDataById($productId,$storeId['store_id']);

                $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');
                
                $synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"put");
                
            }
        }

    }*/

    
    public function catalogControllerCategoryDelete($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        $databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();

        foreach($storeIds as $storeId){

            if($_helper->IsActive($storeId['store_id'])){

                $category = $observer->getCategory();        
                
                $categoryId=$category->getId();
                
                $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

                $objectType="categories";

                $object=$databaseObject->getCategoryDataById($categoryId,$storeId['store_id']);
                
                $synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"delete");

            }
        }

    }

    public function catalogControllerProductDelete($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        $databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();

        foreach($storeIds as $storeId){

            if($_helper->IsActive($storeId['store_id'])){

                $product = $observer->getProduct();        
            
                $productId=$product->getId();
            
                $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

                $objectType="products";

                $object=$databaseObject->getProductDataById($productId,$storeId['store_id']);
                
                $synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"delete");

            }
        }
    }

    public function controllerActionPostdispatchAdminhtmlCatalogProductDelete($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        $databaseObject=Mage::getModel('upseller_clouldsearch/database');

        $storeIds=$databaseObject->getStores();

        foreach($storeIds as $storeId){

            if($_helper->IsActive($storeId['store_id'])){

                $request=Mage::app()->getRequest()->getParams();
            
                $productId=$request['id'];
            
                $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

                $objectType="products";

                $object=$databaseObject->getProductDataById($productId,$storeId['store_id']);
                    
                $synchronization->syncronizationToCloud($object,$objectType,$storeId['store_id'],"delete");

            }
        }
    }

   
    public function cronSynchronization($observer){

        //$_helper=Mage::helper('upseller_clouldsearch');

        //if($_helper->IsActive()){

            $queue=Mage::getModel('upseller_clouldsearch/queue');
            $queue->addQueue();
        //}
        
    }

    public function runCronSynchronization($observer){

        //$_helper=Mage::helper('upseller_clouldsearch');

        //if($_helper->IsActive()){

            $queue=Mage::getModel('upseller_clouldsearch/queue');
            $queue->runQueue();
        //.}
        
    }


    public function checkoutCartSaveAfter($observer){
       
        
        //$postdata=Mage::app()->getRequest()->getPost();

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){
        
            $upsellerCloudsearchItems = Mage::registry('upseller_cloudsearch_items');

            if(is_array($upsellerCloudsearchItems)){

                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $allItem = $quote->getAllVisibleItems();

                $currentItem=array();

                foreach($allItem as $item){
                    if($upsellerCloudsearchItems['method']=="add"){
                        if($upsellerCloudsearchItems['item_id']==$item->getProductId()){
                           $currentItem=$item;
                        }
                    }elseif($upsellerCloudsearchItems['method']=="update"){
                        if($upsellerCloudsearchItems['item_id']==$item->getItemId()){
                           $currentItem=$item;
                        }
                    }
                }
                
                if(count($currentItem)!=0){

                        $trackObject=[];

                        $csaKeywordId=Mage::getModel('core/cookie')->get('csa_keyword_id');
                        $csaSessionId=Mage::getModel('core/cookie')->get('csa_session_id');

                        if($upsellerCloudsearchItems['method']=="add"){

                            $trackObject['item_id']=$currentItem->getItemId();
                            $trackObject['session_id']=$csaSessionId;
                            $trackObject['keyword_id']=$csaKeywordId;
                            $trackObject['name']=$currentItem->getName();
                            $trackObject['sku']=$currentItem->getSku();
                            $trackObject['unique_id']=$currentItem->getProductId();
                            $trackObject['qty']=$currentItem->getQty();
                            $trackObject['amount']=$currentItem->getRowTotalInclTax();
                            $trackObject['is_removed']=false;

                        }elseif($upsellerCloudsearchItems['method']=="update"){

                            $trackObject['item_id']=$currentItem->getItemId();
                            $trackObject['session_id']=$csaSessionId;
                            $trackObject['keyword_id']=$csaKeywordId;
                            $trackObject['name']=$currentItem->getName();
                            $trackObject['sku']=$currentItem->getSku();
                            $trackObject['unique_id']=$currentItem->getProductId();
                            $trackObject['qty']=$currentItem->getQty();
                            $trackObject['amount']=$currentItem->getRowTotalInclTax();
                            $trackObject['is_removed']=false;

                        }

                        

                        $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

                        $synchronization->trackeventToCloud("addtocart",$trackObject,"items");

                }
                
                
            }
            
        }

        if(Mage::registry('upseller_cloudsearch_items')){
            Mage::unregister('upseller_cloudsearch_items');
        }
                
    }

    public function cartProductAddAfter($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){

            Mage::unregister('upseller_cloudsearch_items');

            $currentItem = $observer->getEvent()->getQuoteItem();

            $postdata=Mage::app()->getRequest()->getPost();

            $registryData=['method'=>"add","item_id"=>$postdata['product']];

            Mage::register('upseller_cloudsearch_items', $registryData);
        }

    }

    public function cartProductUpdateAfter($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){

            Mage::unregister('upseller_cloudsearch_items');
            
            $info = $observer->getEvent()->getInfo();

            $registryData=['method'=>"update","item_id"=>key($info)];

            Mage::register('upseller_cloudsearch_items', $registryData);
        }

    }

    public function salesQuoteRemoveItem($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){

            $product = $observer->getEvent()->getProduct();
            $currentItem = $observer->getEvent()->getQuoteItem();

            $trackObject=[];

            $csaKeywordId=Mage::getModel('core/cookie')->get('csa_keyword_id');
            $csaSessionId=Mage::getModel('core/cookie')->get('csa_session_id');

            $trackObject['item_id']=$currentItem->getItemId();
            $trackObject['session_id']=$csaSessionId;
            $trackObject['keyword_id']=$csaKeywordId;
            $trackObject['name']=$currentItem->getName();
            $trackObject['sku']=$currentItem->getSku();
            $trackObject['unique_id']=$currentItem->getProductId();
            $trackObject['qty']=$currentItem->getQty();
            $trackObject['amount']=$currentItem->getRowTotalInclTax();
            $trackObject['is_removed']=true;

            $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

            $synchronization->trackeventToCloud("removetocart",$trackObject,"items");

        }

    }


    public function checkoutSubmitAllAfter($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){

            $order = $observer->getEvent()->getOrder();
            
            $items=[];

            foreach($order->getAllVisibleItems() as $item){
                $items[]=$item->getQuoteItemId();
            }

            //print_r($items);

            $trackObject=[];

            $csaSessionId=Mage::getModel('core/cookie')->get('csa_session_id');

            $trackObject['order_id']=$order->getId();
            $trackObject['session_id']=$csaSessionId;
            $trackObject['order_num']=$order->getIncrementId();
            $trackObject['total_amount']=$order->getGrandTotal();
            $trackObject['customer']=[
                "name"  => $order->getBillingAddress()->getName(),
                "email" => $order->getBillingAddress()->getEmail(),
            ];
            $trackObject['items']['data']=$items;
            $trackObject['status']=$order->getStatus();

            $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

            $synchronization->trackeventToCloud("placeorder",$trackObject,"orders");

        }

        //print_r($trackObject);
        //exit;
    }


}
?>