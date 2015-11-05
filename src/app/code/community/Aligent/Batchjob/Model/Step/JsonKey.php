<?php

/**
 * Generic import step to iterate through all of the records in a JSON key.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_JsonKey extends Aligent_Batchjob_Model_Step_Abstract {

    protected $_vJsonKey;

    public function __construct($aArgs) {
        parent::__construct($aArgs);

        $this->_vJsonKey = (string) $this->_oStepConfig->jsonKey;
    }


    public function run() {
        $aJson = $this->_oParentJob->getJson();

        if (array_key_exists($this->_vJsonKey, $aJson)) {
            $this->getLogger()->log("Begin processing of ".count($aJson[$this->_vJsonKey])." {$this->_vJsonKey} items...");
            foreach ($aJson[$this->_vJsonKey] as $iIdx => $aRecord) {
                $this->setRecordIndex($iIdx);
                $this->processRecord($aRecord);
            }
            $this->getLogger()->log("Completed processing of ".count($aJson[$this->_vJsonKey])." {$this->_vJsonKey} items.");
        }

    }

}