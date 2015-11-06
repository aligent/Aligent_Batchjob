<?php

/**
 * Abstract class from which all specific job classes should inherit.  Provides
 * helper function for setting up child step classes.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
abstract class Aligent_Batchjob_Model_Job_Abstract extends Varien_Object {

    protected $_oJobConfig;
    protected $_vJobCode;
    protected $_aSteps = array();

    public function __construct($aArgs) {
        $this->_vJobCode = $aArgs[0];
        $this->_oJobConfig = $aArgs[1];

        ini_set('memory_limit', '3000M');

        parent::__construct();
    }

    /**
     * Call to initiate processing of a job.
     *
     * @return void
     */
    abstract function run();


    /**
     * @param $vStepCode
     * @return Aligent_Batchjob_MOdel_Step_Abstract
     */
    protected function _getStep($vStepCode) {
        if (!array_key_exists($vStepCode, $this->_aSteps)) {
            $oStepConfig = $this->_oJobConfig->steps->{$vStepCode};
            $this->_aSteps[$vStepCode] = Mage::getModel($oStepConfig->model, array($vStepCode, $oStepConfig, $this))->setLogger($this->getLogger());
        }
        return $this->_aSteps[$vStepCode];
    }


    /**
     * Get the job code for this job.
     *
     * @return string Job code
     */
    public function getJobCode() {
        return $this->_vJobCode;
    }

}