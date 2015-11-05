<?php

/**
 * Fetch all files matching a specified glob from a file transport
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_FetchFile extends Aligent_Batchjob_Model_Step_Abstract {

    protected $_vFileType;
    protected $_vConfigSet;


    public function __construct($aArgs) {
        parent::__construct($aArgs);

        $this->_vFileType = (string)$this->_oStepConfig->fileType;
        $this->_vConfigSet = (string)$this->_oStepConfig->configSet;
    }


    public function run() {
        $this->getLogger()->log("Looking for files of type: ".$this->_vFileType, Zend_Log::INFO);

        $oTransport = Aligent_Batchjob_Model_Transport_Factory::getTransport($this->getLogger(), $this->_vConfigSet);

        if (!$oTransport->importFileExists($this->_vFileType)) {
            // Stop the job, there's nothing to do.
            $this->getLogger()->log("No more files.  Stopping job.", Zend_Log::INFO);
            return false;
        }

        $vLocalFileName = $oTransport->fetchImportFile($this->_vFileType);

        $this->getParentJob()->setFilename($vLocalFileName);
        return true;

    }


    protected function _getFileList() {
        $oFiles = new GlobIterator($this->_vFileGlob, FilesystemIterator::KEY_AS_PATHNAME);
        $aFileNames = array();
        foreach ($oFiles as $vFileName => $oFileInfo) {
            $aFileNames[] = $vFileName;
        }

        // File naming spec is designed so that when file list is sorted by name
        // the oldest comes first.
        sort($aFileNames);

        return $aFileNames;
    }

}