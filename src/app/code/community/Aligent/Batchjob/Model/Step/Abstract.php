<?php

/**
 * This class sets up a chain of command allowing different objects to hook into
 * the processing chain for a record as individual tasks.  Each task declares
 * itself in it's module's config.xml
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
abstract class Aligent_Batchjob_Model_Step_Abstract extends Varien_Object {

    protected $_vStepCode;
    protected $_oStepConfig;
    protected $_oParentJob;
    protected $_oParentStep = null;
    protected $_aChildren = array();


    public function __construct($aArgs) {
        $this->_vStepCode = $aArgs[0];
        $this->_oStepConfig = $aArgs[1];
        $this->_oParentJob = $aArgs[2];
        if (array_key_exists(3, $aArgs)) {
            $this->_oParentStep = $aArgs[3];
        }
        parent::__construct();
    }


    /**
     * Called to initiate processing on a given step.
     *
     * @return False to stop further processing of subsequent steps.
     */
    abstract function run();


    /**
     * Pass the current record off to an itemtask for processing.
     *
     * @param $aRecord array Array of input data
     */
    public function processRecord($aRecord) {
        // Iterate through each of the itemTasks to process this record.
        foreach ($this->_getAllItemTasks() as $oItemTask) {
            $bContinue = $oItemTask->process($aRecord);
            if ($bContinue === false) {
                break;
            }
        }
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
     * Returns an array if all item processing tasks.
     *
     * @return array An array of Aligent_Batchjob_Model_ItemTask_Abstract ready to process an item.
     */
    protected function _getAllItemTasks() {
        $aTasks = array();
        foreach ($this->_oStepConfig->itemTasks->children() as $vItemTaskCode => $oItemConfig) {
            $aTasks[$vItemTaskCode] = $this->_getItemTask($vItemTaskCode);
        }
        return $aTasks;
    }


    /**
     * Returns a single item processing task configured and ready to use.
     *
     * @return Aligent_Batchjob_Model_ItemTask_Abstract
     */
    protected function _getItemTask($vTaskCode) {
        return $this->_getChildObjects('itemTasks', $vTaskCode);
    }


    /**
     * Spins up a child step or itemTask object based on the XML definitions.
     *
     * @param $vChildNode string XML tag for parent node (e.g. "itemTasks" or "steps")
     * @param $vCode string Unique code identifying the child.
     * @return Aligent_Batchjob_Model_ItemTask_Abstract|Aligent_Batchjob_Model_Step_Abstract
     */
    protected function _getChildObjects($vChildNode, $vCode) {
        if (!array_key_exists($vCode, $this->_aChildren)) {
            $oStepConfig = $this->_oStepConfig->{$vChildNode}->{$vCode};
            $this->_aChildren[$vCode] = Mage::getModel($oStepConfig->model, array($vCode, $oStepConfig, $this->_oParentJob, $this))->setLogger($this->getLogger());
        }
        return $this->_aChildren[$vCode];
    }
}