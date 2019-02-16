<?php
class Upseller_Clouldsearch_Model_System_Config_Source_Environment_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'conversiotech.in',
                'label' => 'Development',
            ),
            array(
                'value' => 'upsellerapp.com',
                'label' => 'Live',
            ),
        );
    }
}