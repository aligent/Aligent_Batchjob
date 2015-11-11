<?php

/**
 * Open a CSV file and assign it's file handle to the parent job.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_OpenCsv extends Aligent_Batchjob_Model_Step_Abstract {


    /**
     * Called to initiate processing on a given step.
     *
     * @return False to stop further processing of subsequent steps.
     */
    public function run() {
        $vLocalFileName = $this->getParentJob()->getFilename();
        $this->getLogger()->log("Opening CSV file... ".$vLocalFileName, Zend_Log::INFO);

        $hCsv = fopen($vLocalFileName, $this->_getFileMode());
        $this->getParentJob()->setFileHandle($hCsv);
        return true;
    }


    /**
     * Returns the file mode defined in config.xml.  Defaults to "r" if no mode
     * was defined.
     *
     * @return string File mode
     */
    protected function _getFileMode() {
        $vFileMode = $this->getParentJob()->getFileMode();
        if ($vFileMode === null) {
            $vFileMode = 'r';
        }
        return $vFileMode;
    }

}