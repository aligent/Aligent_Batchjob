<?php

/**
 * Log level source
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_System_Config_Source_Log_Level {
    
    public function toOptionArray() {
        return array(
            array('value' => 0,  'label' => 'EMERG'),
            array('value' => 1,  'label' => 'ALERT'),
            array('value' => 2,  'label' => 'CRIT'),
            array('value' => 3,  'label' => 'ERR'),
            array('value' => 4,  'label' => 'WARN'),
            array('value' => 5,  'label' => 'NOTICE'),
            array('value' => 6,  'label' => 'INFO'),
            array('value' => 7,  'label' => 'DEBUG'),
        );
    }
}
