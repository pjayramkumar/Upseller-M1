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
                           <![CDATA[<link rel="stylesheet" type="text/css" href="'.$_helper->getProtocol().'://'.$_helper->getClusterKey($storeId).'.'.$_helper->getSubdomain($storeId).'.'.$_helper->getSearchDomain($storeId).'/csa/cloudesearchauto_v1.0.0.css">]]>
                        </text>
                      </action>
                  </block>
                  <block type="core/text" name="csascript">
                      <action method="setText">
                        <text>
                           <![CDATA[<script type="text/javascript" src="'.$_helper->getProtocol().'://'.$_helper->getClusterKey($storeId).'.'.$_helper->getSubdomain($storeId).'.'.$_helper->getSearchDomain($storeId).'/csa/cloudesearchauto_v1.0.0.js"></script>]]>
                        </text>
                      </action>
                   </block>
                   <block type="core/text" name="csasalectoscript">
                      <action method="setText">
                        <text>
                           <![CDATA[<script type="text/javascript" src="'.$_helper->getProtocol().'://'.$_helper->getClusterKey($storeId).'.'.$_helper->getSubdomain($storeId).'.'.$_helper->getSearchDomain($storeId).'/csa/salecto_v1.0.0.js"></script>]]>
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
       
        //Mage::log(print_r("checkoutCartSaveAfter",true), null, 'upseller.log', true);
        //$postdata=Mage::app()->getRequest()->getPost();

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){
        
            $upsellerCloudsearchItems = Mage::registry('upseller_cloudsearch_items');
            //Mage::log(print_r($upsellerCloudsearchItems,true), null, 'upseller.log', true);
            if(is_array($upsellerCloudsearchItems)){

                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $allItem = $quote->getAllVisibleItems();

                $currentItem=array();

                foreach($allItem as $item){
                    //Mage::log(print_r($upsellerCloudsearchItems['item_id'].'=='.$item->getProductId(),true), null, 'upseller.log', true);
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
                            $trackObject['upseller_search']=$upsellerCloudsearchItems['upseller_search'];


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
                            $trackObject['upseller_search']=false;

                        }

                        
                        //Mage::log(print_r($trackObject,true), null, 'upseller.log', true);
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
            //Mage::log(print_r("Product ID BEFORE".$currentItem->getProductId(),true), null, 'upseller.log', true);
            $postdata=Mage::app()->getRequest()->getParams();
            
            $currentItemId=$currentItem->getProductId();
            if($currentItem->getProduct()->getParentProductId()){
                $currentItemId=$currentItem->getProduct()->getParentProductId();
            }

            //if($currentItem->getParentItemId()){
            //    $currentItem = Mage::getModel('sales/quote_item')->load($currentItem->getParentItemId());
            //    Mage::log(print_r("Product ID AFTER".$currentItem->getProductId(),true), null, 'upseller.log', true);
            //}

             //Mage::log(print_r("getParentProductId ".$currentItemId,true), null, 'upseller.log', true);
            //Mage::log(print_r($currentItem->debug(),true), null, 'upseller.log', true);

            if($currentItem->getProductId()){
                //Mage::log(print_r($postdata,true), null, 'upseller.log', true);
                //$registryData=['method'=>"add","item_id"=>$postdata['product']];
                if(isset($postdata['upseller_search'])){
                    $registryData=['method'=>"add","item_id"=>$currentItemId,"upseller_search"=>1];
                }else{
                    $registryData=['method'=>"add","item_id"=>$currentItemId,"upseller_search"=>0];
                }

                Mage::register('upseller_cloudsearch_items', $registryData);
            }
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
                $itemId=$item->getQuoteItemId();
                if($item->getParentItemId()){
                    $itemId=$item->getParentItemId();
                }

                $items[]=$itemId;
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
            //Mage::log(print_r($trackObject,true), null, 'upseller.log', true);
            $synchronization->trackeventToCloud("placeorder",$trackObject,"orders");

        }

        //print_r($trackObject);
        //exit;
    }

    public function checkoutOnepageControllerSuccessAction($observer){

        $_helper=Mage::helper('upseller_clouldsearch');

        if($_helper->IsActive()){

            $orderIds=$observer->getData('order_ids');
            
            if(is_array($orderIds)){   

                foreach($orderIds as $id){

                    $order=Mage::getModel('sales/order')->load($id);

                    $items=[];

                    foreach($order->getAllVisibleItems() as $item){

                        $itemId=$item->getQuoteItemId();
                        if($item->getParentItemId()){
                            $itemId=$item->getParentItemId();
                        }

                        $items[]=$itemId;
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
                    //Mage::log(print_r($trackObject,true), null, 'upseller.log', true);
                    $synchronization=Mage::getModel('upseller_clouldsearch/synchronization');

                    $synchronization->trackeventToCloud("placeorder",$trackObject,"orders");

                }
            }    

        }

    }


}
?>