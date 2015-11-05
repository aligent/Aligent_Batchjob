<?php

/**
 * Generic import step to iterate through all of the records in a CSV file.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_IterateCsv extends Aligent_Batchjob_Model_Step_Abstract {

    public function run() {

        $row = 1;
        while (!feof($this->getParentJob()->getFileHandle())) {
            $this->setRowIdx($row++);
            $aRecord = fgetcsv($this->getParentJob()->getFileHandle(), 0, $this->getParentJob()->getDelimiter(), $this->getParentJob()->getEnclosure(), $this->getParentJob()->getEscape());
            $this->parseRecord($aRecord);
            $this->processRecord($aRecord);

        }

        // Rewind the file handle back to the start in case we want to iterate again.
        rewind($this->getParentJob()->getFileHandle());
    }


    /**
     * Extract the usable data from the CSV record.  Useful for reformatting the
     * record as an associative array.
     *
     * @param array $aRecord Record from CSV file
     */
    public function parseRecord(&$aRecord) {
        // By default does nothing, but can be overridden to reformat record as
        // associative array for ease of handling later.
    }
}