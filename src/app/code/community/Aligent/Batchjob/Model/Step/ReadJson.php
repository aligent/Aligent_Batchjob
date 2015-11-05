<?php

/**
 * Open a JSON file and assign it's contents to the parent job.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_ReadJson extends Aligent_Batchjob_Model_Step_Abstract {

    public function run() {
        $vLocalFileName = $this->getParentJob()->getFilename();
        $this->getLogger()->log("Reading JSON file...".$vLocalFileName, Zend_Log::INFO);

        $oJson = Mage::helper('core')->jsonDecode(file_get_contents($vLocalFileName));
        $this->getParentJob()->setJson($oJson);
        return true;
    }

}