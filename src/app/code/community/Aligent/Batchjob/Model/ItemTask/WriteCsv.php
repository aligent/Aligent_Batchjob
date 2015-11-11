<?php


/**
 * A generic itemTask to export a record to a CSV file.  Use with the
 * batchJob/step_initCsvWriter step to open the CSV file and define the header
 * row/fields to export.  Use batchjob/step_closeCsv to close the CSV file once
 * finished writing.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_ItemTask_WriteCsv extends Aligent_Batchjob_Model_ItemTask_Abstract {

    /**
     * Process the record.  This method may return boolean false to prevent any
     * further processing steps.
     *
     * @param array $aItem The current record.
     * @return boolean May return false to prevent further tasks from running.
     */
    function process(&$aItem) {
        /** @var Aligent_Batchjob_Model_Simplefilewriter $oFileWriter */
        $oFileWriter = $this->getParentJob()->getFileWriter();
        $oFileWriter->writeDataRow($aItem->getData());
        return true;
    }


}