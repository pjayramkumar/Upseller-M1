<?php 
class Upseller_Clouldsearch_Model_Synchronization extends Mage_Core_Model_Abstract {

	protected $_databaseObject=null;

	protected $_batch;

	public function __construct(){

		$this->_databaseObject=Mage::getModel('upseller_clouldsearch/database');
		
	}

	public function continueSyn($synchronizationArray){

		$this->_batch=Mage::getStoreConfig("upseller_clouldsearch/settings/syncrobatch",$synchronizationArray['store']);

		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();



		return $currentBatch=$this->__findCurrentBatch($synchronizationArray);


	}


	public function initialization($synchronizationArray){

		$this->_batch=Mage::getStoreConfig("upseller_clouldsearch/settings/syncrobatch",$synchronizationArray['store']);
		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();

		return $this->__initialization($synchronizationArray,$cloudseachSession);
	}

	protected function __initialization($synchronizationArray,$cloudseachSession){

		$sessionArray=[];


		$categoryArray=$this->__initCatalog($synchronizationArray['is_categories'],"categories",$synchronizationArray['store']);

		$productArray=$this->__initCatalog($synchronizationArray['is_products'],"products",$synchronizationArray['store']);

		$sessionArray['categories']=$categoryArray;
		$sessionArray['products']=$productArray;



		if($cloudseachSession==null){
			Mage::Helper('upseller_clouldsearch/session')->setCloudSearchSession($sessionArray);
			return true;
		}else{
			$this->__reBuildSession($sessionArray,$cloudseachSession/*,$synchronizationArray['store']*/);
			return true;
		}


	}

	protected function __reBuildSession($sessionArray,$cloudseachSession/*,$synchronizationStore*/){
		//return $sessionArray;
		//$store=$this->_databaseObject->getStore($synchronizationStore);

		
		
		Mage::Helper('upseller_clouldsearch/session')->setCloudSearchSession(array_merge($sessionArray,$cloudseachSession));
		return array_merge($sessionArray,$cloudseachSession);
	}

	protected function __findCurrentBatch($synchronizationArray){

		$cloudseachSession=Mage::Helper('upseller_clouldsearch/session')->getCloudSearchSession();

		$store=$this->_databaseObject->getStore($synchronizationArray['store']);
		
		//foreach($stores as $store){
		
			if($cloudseachSession['categories'][$store['code']]['categories_finished']==0){

				$updatedSession=$this->synchronizationCategoryData($cloudseachSession['categories'][$store['code']],$store['store_id']);
				$cloudseachSession['categories'][$store['code']]=$updatedSession;
				Mage::Helper('upseller_clouldsearch/session')->setCloudSearchSession($cloudseachSession);
				return true;
			}

			if($cloudseachSession['products'][$store['code']]['products_finished']==0){
				
				$updatedSession=$this->synchronizationProductData($cloudseachSession['products'][$store['code']],$store['store_id']);
				$cloudseachSession['products'][$store['code']]=$updatedSession;
				Mage::Helper('upseller_clouldsearch/session')->setCloudSearchSession($cloudseachSession);
				return true;
			}
		//}		


	}

	protected function synchronizationProductData($cloudseachSessionProducts,$storeId){

		$currentChunk=$cloudseachSessionProducts['products_current_chunk'];

		$batch=$cloudseachSessionProducts['products_batch'];

		$productsTotalChunk=$cloudseachSessionProducts['products_total_chunk'];


		if($productsTotalChunk<$currentChunk+1){
			return true;
		}

		if($currentChunk==0){
			$start=0;
			$limit=$batch;
		}else{
			$start=$currentChunk*$batch;
			$limit=$batch;
		}

		$productsData=$this->_databaseObject->getProductData($limit,$start,$storeId);

		//Zend_Debug::dump($productsData);
		//exit;
		// Syncronization Logic

			$this->syncronizationToCloud($productsData,"products",$storeId);

		// End


		if($productsTotalChunk==$currentChunk+1){
			$cloudseachSessionProducts['products_finished']=1;
		}

		$cloudseachSessionProducts['products_current_chunk']=$currentChunk+1;
		//Mage::log(print_r($cloudseachSessionCategories,true),null,"cloudsearch.log",true);
		return $cloudseachSessionProducts;

	}


	protected function synchronizationCategoryData($cloudseachSessionCategories,$storeId){

		
		$currentChunk=$cloudseachSessionCategories['categories_current_chunk'];

		$batch=$cloudseachSessionCategories['categories_batch'];

		$categoriesTotalChunk=$cloudseachSessionCategories['categories_total_chunk'];


		if($categoriesTotalChunk<$currentChunk+1){
			return true;
		}

		if($currentChunk==0){
			$start=0;
			$limit=$batch;
		}else{
			$start=$currentChunk*$batch;
			$limit=$batch;
		}

		$categoryData=$this->_databaseObject->getCategoryData($limit,$start,$storeId);
		
		//print_r($categoryData);
		//exit;
		// Syncronization Logic

			$this->syncronizationToCloud($categoryData,"categories",$storeId);

		// End

		if($categoriesTotalChunk==$currentChunk+1){
			$cloudseachSessionCategories['categories_finished']=1;
		}

		$cloudseachSessionCategories['categories_current_chunk']=$currentChunk+1;
		//Mage::log(print_r($cloudseachSessionCategories,true),null,"cloudsearch.log",true);
		return $cloudseachSessionCategories;

	}


	protected function __initCatalog($isCatalog,$catalogType,$poststore){

		$returnArray=[];

		$store=$this->_databaseObject->getStore($poststore);
		
		if($isCatalog){

			if($catalogType=="categories"){
				$totalCatalog=$this->_databaseObject->getTotalCategories($poststore);
			}else{
				$totalCatalog=$this->_databaseObject->getTotalProducts($poststore);
			}	
				
			$totalCatalogChunk=ceil($totalCatalog/$this->_batch);

			$returnArray[$store['code']][$catalogType.'_batch']=$this->_batch;
			$returnArray[$store['code']][$catalogType.'_finished']=0;
			$returnArray[$store['code']][$catalogType.'_total']=$totalCatalog;
			$returnArray[$store['code']][$catalogType.'_total_chunk']=$totalCatalogChunk;
			$returnArray[$store['code']][$catalogType.'_current_chunk']=0;
				

		}else{


			$returnArray[$store['code']][$catalogType.'_batch']=$this->_batch;
			$returnArray[$store['code']][$catalogType.'_finished']=1;
			$returnArray[$store['code']][$catalogType.'_total']=0;
			$returnArray[$store['code']][$catalogType.'_total_chunk']=0;
			$returnArray[$store['code']][$catalogType.'_current_chunk']=0;

		}

		return $returnArray;

	}


	public function syncronizationToCloud($objects,$objectType,$storeId=false,$method="put"){
		

		$_helper=Mage::helper('upseller_clouldsearch');

		

		$webserviceUrl=$_helper->getProtocol()."://".$_helper->getClusterKey($storeId).".".$_helper->getSubdomain($storeId).".".$_helper->getSearchDomain($storeId)."/api/synchronize/object";

		//print_r($webserviceUrl);
		//exit;

		//$webserviceUrl=$_helper->getProtocol()."://".$_helper->getCloudsearchUid($storeId).".".$_helper->getSubdomain($storeId).".".$_helper->getSearchDomain($storeId)."/api/synchronize/object";
		//$webserviceUrl = "http://".$_helper->getCloudsearchUid($storeId).".cloudsearch.".$_helper->getSearchDomain($storeId)."/api/synchronize/object";

		if($storeId===false){
			$postData=['method'=>$method,'object'=>$objects,"object_type"=>$objectType,"store_id"=>"","cloudsearch_uid"=>$_helper->getCloudsearchUid($storeId),"cloudsearch_key"=>$_helper->getCloudsearchKey($storeId),"version"=>$_helper->_version];
		}else{
			$postData=['method'=>$method,'object'=>$objects,"object_type"=>$objectType,"store_id"=>$storeId,"cloudsearch_uid"=>$_helper->getCloudsearchUid($storeId),"cloudsearch_key"=>$_helper->getCloudsearchKey($storeId),"version"=>$_helper->_version];
		}

		//echo $webserviceUrl;
		
		//echo "<pre>";
		//print_r($postData);
		//echo "<pre>";
//		$postData = array_map( 'intval', array_filter($postData['object'][1], 'is_numeric' ) );;
//		print_r(http_build_query($postData));
		//exit;
		//return;
//		print_r($webserviceUrl);

		$ch = curl_init($webserviceUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
		curl_setopt($ch,CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postData));
		curl_setopt($ch, CURLOPT_HEADER, 1);

		// execute!
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);

		//echo "<pre>"; print_r($response); echo "<pre>"; exit;
		
//		Mage::log("================================", null, 'upseller-sync-log.log');
//		Mage::log("object_type => ".$objectType, null, 'upseller-sync-log.log');
//		Mage::log("store_id => ".$storeId, null, 'upseller-sync-log.log');
		//foreach($objects as $key => $object){
//			Mage::log("object_id => ".$object['entity_id'], null, 'upseller-sync-log.log');
		//}
		
		if ($response === FALSE) {
		   	//echo "cURL Error: " . curl_error($ch);
		   	//Mage::log("cURL Error: " . curl_error($ch), null, 'upseller-sync-log.log');
		} else {
			//var_dump($response);
		}
		

		//echo "<pre>";
		// print_r($postData);
		 //print_r($response);
		 //print_r($info);
		 //exit;

		//print_r($objects);
		//exit;
		//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/info_'.date("j.n.Y").'.txt', print_r($info,true), FILE_APPEND);

		
		if($info['http_code']=="200"){
			// close the connection, release resources used
			curl_close($ch);
			Mage::getSingleton('core/session')->setSyncronizationSuccess(true);
			// sleep for 10 seconds
			#sleep(1);
			return true;
		}else{
			// close the connection, release resources used
			curl_close($ch);
			Mage::getSingleton('core/session')->setSyncronizationSuccess(false);
			// sleep for 10 seconds
			#sleep(1);
			// print_r($info);
			// exit;
			return false;
		}
		
	}

	public function trackeventToCloud($event,$trackdata,$objectType){

		$_helper=Mage::helper('upseller_clouldsearch');

		$csaKeywordId=Mage::getModel('core/cookie')->get('csa_keyword_id');
        $csaSessionId=Mage::getModel('core/cookie')->get('csa_session_id');
		
		$storeId = Mage::app()->getStore()->getStoreId();

		$webserviceUrl=$_helper->getProtocol()."://".$_helper->getClusterKey($storeId).".".$_helper->getSubdomain($storeId).".".$_helper->getSearchDomain($storeId)."/api/indices/trackevent";

		//$webserviceUrl=$_helper->getProtocol()."://".$_helper->getCloudsearchUid($storeId).".".$_helper->getSubdomain($storeId).".".$_helper->getSearchDomain($storeId)."/api/indices/trackevent";
		
		

		if($storeId===false){
			$postData=['event'=>$event,'trackdata'=>$trackdata,"cms"=>$_helper->_cms,"storeid"=>"","apiuid"=>$_helper->getCloudsearchUid($storeId),"apikey"=>$_helper->getCloudsearchKey($storeId),"cmsversion"=>$_helper->_version,"csa_session_id"=>$csaSessionId,"csa_keyword_id"=>$csaKeywordId,"object_type"=>$objectType];
		}else{
			$postData=['event'=>$event,'trackdata'=>$trackdata,"cms"=>$_helper->_cms,"storeid"=>$storeId,"apiuid"=>$_helper->getCloudsearchUid($storeId),"apikey"=>$_helper->getCloudsearchKey($storeId),"cmsversion"=>$_helper->_version,"csa_session_id"=>$csaSessionId,"csa_keyword_id"=>$csaKeywordId,"object_type"=>$objectType];
		}


		$ch = curl_init($webserviceUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
		curl_setopt($ch,CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// execute!
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		#print_r($info);
		#exit;
		if($info['http_code']=="200"){
			// close the connection, release resources used
			curl_close($ch);
			// sleep for 10 seconds
			//sleep(1);
			return true;
		}else{
			// close the connection, release resources used
			curl_close($ch);
			// sleep for 10 seconds
			//sleep(1);
			return false;
		}




	}








}