<?php
class Upseller_Clouldsearch_Block_Adminhtml_Synchronization_Index extends Mage_Adminhtml_Block_Template
{

    public function __construct(){

       
    }

    public function getMigrateButton()
    {
        
        $startLabel = 'Start Synchronization';
        $startAction = 'startSynchronization';
        
        return $this->_makeButton($startLabel, $startAction, false);
    }

    private function _makeButton($label, $action, $disabled = false)
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'clouldsearch_synchronization_start',
                'label' => $this->helper('upseller_clouldsearch')->__($label),
                'disabled' => $disabled,
                'onclick' => "startSynchronization();"
            ));

        return $button->toHtml();
    }

}
?>