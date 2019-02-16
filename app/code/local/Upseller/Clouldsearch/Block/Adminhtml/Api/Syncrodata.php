<?php

class Upseller_Clouldsearch_Block_Adminhtml_Api_Syncrodata extends Mage_Adminhtml_Block_Template
{
    public function getAssests(){
        $syncrodata=Mage::getModel('upseller_clouldsearch/syncrodata');
        $returnAssests=$syncrodata->getAssets();
        return $returnAssests;
    }

}
?>