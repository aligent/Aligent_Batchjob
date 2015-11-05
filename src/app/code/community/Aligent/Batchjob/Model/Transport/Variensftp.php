<?php

/**
 * Wrap Varien's SFTP class and add a method to get the errors from the 
 * underlying Net_SFTP class. 
 * 
 */
class Aligent_Batchjob_Model_Transport_Variensftp extends Varien_Io_Sftp {
   
    /**
     * Return errors from underlying Net_SFTP class
     * 
     * @return string
     */
    public function getErrors() {
        return implode("\n", $this->_connection->getSFTPErrors());
    }
}
