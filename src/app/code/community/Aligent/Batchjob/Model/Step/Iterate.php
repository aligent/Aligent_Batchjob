<?php

/**
 * Generic step to iterate through all of the records in anything that's
 * iterable, such as an array, collection, or similar.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_Iterate extends Aligent_Batchjob_Model_Step_Abstract {

    public function run() {
        $row = 1;
        $aRecords = $this->getParentJob()->getRecords();
        if ($aRecords != null) {
            foreach ($aRecords as $aRecord) {
                $this->setRowIdx($row++);
                $this->processRecord($aRecord);
            }
        }
    }

}