<?php

class Aligent_Batchjob_Model_ItemTask_WriteCsv extends Aligent_Batchjob_Model_ItemTask_Abstract {
    /**
     * Process the record.  This method may return boolean false to prevent any further processing steps.
     * @param array $aItem The current record.
     * @return boolean May return false to prevent further tasks from running.
     */
    function process(&$aItem) {

        /** @var Aligent_Batchjob_Model_Simplefilewriter $oFileWriter */
        $oFileWriter = $this->getParentJob()->getFileWriter();
        $oFileWriter->writeDataRow($aItem->getData());

    }


}