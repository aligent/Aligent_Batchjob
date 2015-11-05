<?php

/**
 * Transport source
 *
 */
class Aligent_Batchjob_Model_System_Config_Source_Transport {
    
    const TRANSPORT_SFTP = 'sftp';
    const TRANSPORT_LOCAL = 'local';
    
    public function toOptionArray() {
        return array(
            array('value' => self::TRANSPORT_SFTP,  'label' => 'SFTP'),
            array('value' => self::TRANSPORT_LOCAL, 'label' => 'Local Folder')
        );
    }
}
