<?php
class Upseller_Clouldsearch_Model_Queue extends Mage_Core_Model_Abstract
{
   // const SUCCESS_LOG = 'upseller_clouldsearch_queue_log.txt';

  //  const ERROR_LOG = 'upseller_clouldsearch_queue_errors.log';

    protected $table;

    protected $dbRes;

    protected $maxSingleJobDataSize = 30 ;

    protected $batch;

    protected $_databaseObject = null;

    protected $synchronization = null;

    public function __construct()
    {
        /** @var Mage_Core_Model_Resource $coreResource */
        $coreResource = Mage::getSingleton('core/resource');

        $this->table = $coreResource->getTableName('upseller_clouldsearch/queue');

        $this->dbRes = $coreResource->getConnection('core_write');

        $this->batch = Mage::getStoreConfig("upseller_clouldsearch/settings/syncrobatch");
        
        $this->_databaseObject = Mage::getModel('upseller_clouldsearch/database');

        $this->synchronization = Mage::getModel('upseller_clouldsearch/synchronization');
    }


    public function add($class , $priority, $method, $data , $storeId)
    {
        $dataChunk=array_chunk($data,$this->batch);
        foreach ($dataChunk as $dataChu) {
            // Insert a row for the new job
            
            $currentTime=Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
            $binds = array(
                'qpriority' => $priority,
                'qclass' => $class,
                'qmethod' => $method,
                'qdata' => json_encode($dataChu),
                'qdata_size' => $this->batch,
                'qstore_id' => $storeId,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            );

            $query = "INSERT INTO " . $this->table . " SET qpriority = :qpriority , qclass = :qclass , qmethod = :qmethod , qdata = :qdata , qdata_size = :qdata_size , qstore_id = :qstore_id , created_at = :created_at , updated_at = :updated_at ";
            $this->dbRes->query( $query, $binds );

           

        }
         $this->dbRes->commit(); 

    }


    public function addQueue(){

        $this->clearCompletedQueue();

        $stores=$this->_databaseObject->getStores();
        foreach($stores as $store){

            $_helper=Mage::helper('upseller_clouldsearch');

            if($_helper->IsActive($store['store_id'])){

                $totalCategories=$this->_databaseObject->getCategoriesIds($store['store_id']);
                $totalProducts=$this->_databaseObject->getProductsIds($store['store_id']);
                $this->add("categories",0,"put",$totalCategories,$store['store_id']);
                $this->add("products",0,"put",$totalProducts,$store['store_id']);
            }
            
        }
        
           
        $this->runQueue();
    }

    public function runQueue(){

        $selectSql="select * from `".$this->table."` where qstatus='pending' and qmax_retries > qretries limit 0,".$this->maxSingleJobDataSize;
        $jobs=$this->dbRes->fetchAll($selectSql);
        $this->runJobs($jobs);
       
    }

    public function clearCompletedQueue(){

        //$deleteSql="delete from `".$this->table."` where qstatus='completed'";
        $deleteSql="TRUNCATE TABLE `".$this->table."`";
        $this->dbRes->query($deleteSql);

    }

    protected function runJobs($jobs){

        foreach ($jobs as $job) {
            
            $pid = getmypid();

            $data=json_decode($job['qdata'],true);
            $storeId=$job['qstore_id'];
            $qclass=$job['qclass'];

            $jobObjects=[];

            if($qclass=="categories"){

               foreach($data as $da){
                 $object=$this->_databaseObject->getCategoryDataById($da['entity_id'],$storeId);
                 $jobObjects[key($object)]=$object[key($object)];
               } 
                 
            }elseif($qclass=="products"){

               foreach($data as $da){
                 $object=$this->_databaseObject->getProductDataById($da['entity_id'],$storeId);
                 $jobObjects[key($object)]=$object[key($object)];
               } 
            }

            
            if($this->synchronization->syncronizationToCloud($jobObjects,$qclass,$storeId)){
                $currentTime=Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
                $this->dbRes->query("update `".$this->table."` set qpid='".$pid."' , qstatus='completed' , updated_at='".$currentTime."' where qjob_id='".$job['qjob_id']."'");
                $this->dbRes->commit(); 
            
            }else{
                $currentTime=Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
                $qretries=$job['qretries']+1;    
                $this->dbRes->query("update `".$this->table."` set qpid='".$pid."' , qerror_log='Internal Serverl Error' , qretries='".$qretries."' , updated_at='".$currentTime."' where qjob_id='".$job['qjob_id']."'");
                $this->dbRes->commit(); 
            }



        }
    }



}
