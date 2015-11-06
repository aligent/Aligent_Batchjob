<?php

/**
 * Open a CSV file and assign it's file handle to the parent job.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_OpenCsv extends Aligent_Batchjob_Model_Step_Abstract {

    public function run() {
        $vLocalFileName = $this->getParentJob()->getFilename();
        $this->getLogger()->log("Openning CSV file...".$vLocalFileName, Zend_Log::INFO);

        $hCsv = fopen($vLocalFileName, $this->_getFileMode());
        $this->getParentJob()->setFileHandle($hCsv);
        return true;
    }


    protected function _getFileMode() {
        $vFileMode = $this->getParentJob()->getFileMode();
        if ($vFileMode === null) {
            $vFileMode = 'r';
        }
        return $vFileMode;
    }

}