<?php

/**
 * Archive a file after processing.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_ArchiveFile extends Aligent_Batchjob_Model_Step_Abstract {

    protected $_vConfigSet;

    public function __construct($aArgs) {
        parent::__construct($aArgs);
        $this->_vConfigSet = (string)$this->_oStepConfig->configSet;
    }


    public function run() {
        $vFilename = $this->_oParentJob->getFilename();

        if (file_exists($vFilename)) {
            $this->getLogger()->log('Archiving file: '.$vFilename, Zend_Log::INFO);

            $oTransport = Aligent_Batchjob_Model_Transport_Factory::getTransport($this->getLogger(), $this->_vConfigSet);
            $oTransport->archiveFile($vFilename);
        }

        return true;
    }

}