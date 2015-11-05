<?php
/**
 * Generic job that simply iterates through all of it's child steps, repeating
 * until a step aborts the job, but enhanced for CSV operations.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 *
 * @method string getDelimiter()
 * @method string getEnclosure()
 * @method string getEscape()
 */
class Aligent_Batchjob_Model_Job_Csv extends Aligent_Batchjob_Model_Job_Generic {

    public function __construct($aArgs) {
        parent::__construct($aArgs);

        $this->setDelimiter((string)$this->_oJobConfig->delimiter);
        $this->setEnclosure((string)$this->_oJobConfig->enclosure);
        $this->setEscape((string)$this->_oJobConfig->escape);
    }

}