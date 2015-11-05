<?php

/**
 * Base class from which all of the other item tasks must inherit.
 */
abstract class Aligent_Batchjob_Model_ItemTask_Abstract extends Varien_Object {

    protected $_vTaskCode;
    protected $_oTaskConfig;
    protected $_oParentJob = null;
    protected $_oParentStep = null;

    public function __construct($aArgs) {
        $this->_vTaskCode = $aArgs[0];
        $this->_oTaskConfig = $aArgs[1];
        $this->_oParentJob = $aArgs[2];
        $this->_oParentStep = $aArgs[3];
    }


    /**
     * For steps that are nested within other steps, return the parent.
     *
     * @return Aligent_Batchjob_Model_Step_Abstract
     */
    public function getParentStep() {
        return $this->_oParentStep;
    }


    /**
     * Returns the parent job object
     * @return Aligent_Batchjob_Model_Job_Abstract
     */
    public function getParentJob() {
        return $this->_oParentJob;
    }


    /**
     * Process the record.  This method may return boolean false to prevent any further processing steps.
     * @param array $aItem The current record.
     * @return boolean May return false to prevent further tasks from running.
     */
    abstract function process(&$aItem);

}