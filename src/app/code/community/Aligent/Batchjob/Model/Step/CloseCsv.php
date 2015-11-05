<?php

/**
 * Close an open CSV file handle.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_CloseCsv extends Aligent_Batchjob_Model_Step_Abstract {

    public function run() {
        $vLocalFileName = $this->getParentJob()->getFilename();
        $this->getLogger()->log("Closing CSV file...".$vLocalFileName, Zend_Log::INFO);

        $hCsv = $this->getParentJob()->getFileHandle();
        fclose($hCsv);
        $this->getParentJob()->unsetFileHandle();
        return true;
    }

}