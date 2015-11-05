<?php

/**
 * Abstract base class for unit testing Jobs/Steps/Tasks built on
 * Aligent_Batchjob.  Consists primarily of helpers for instantiating suitably
 * isolated import objects.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
abstract class Aligent_Batchjob_Test_Model_Abstract extends EcomDev_PHPUnit_Test_Case {

    /**
     * Spin up a job with dummy config data and a mock logger.
     *
     * @param $vAlias string Class alias of desired job
     * @return mixed
     */
    protected function _instantiateJob($vAlias) {
        $aJobParams = array(
            'test', // Step Code
            new Varien_Object(), // Step config
        );

        $oStep = Mage::getModel($vAlias, $aJobParams);
        $oStep->setLogger($this->_instantiateMockLogger());

        return $oStep;
    }


    /**
     * Spin up a step with dummy config data and a mock logger.
     *
     * @param $vAlias string Class alias of desired step
     * @return mixed
     */
    protected function _instantiateStep($vAlias, $vJobAlias = '') {
        $aStepParams = array(
            'test', // Step Code
            new Varien_Object(), // Step config
            ($vJobAlias == '' ? new Varien_Object() : $this->_instantiateJob($vJobAlias)), // Parent Job
            null, // Parent Step
        );

        $oStep = Mage::getModel($vAlias, $aStepParams);
        $oStep->setLogger($this->_instantiateMockLogger());

        return $oStep;
    }


    /**
     * Spin up an itemtask with dummy config data and a mock logger.
     *
     * @param $vAlias string Class alias of desired itemTask
     * @param $vStepAlias string Class alias of parent step to insert into itemTask
     * @return mixed
     */
    protected function _instantiateItemTask($vAlias, $vStepAlias = '', $vJobAlias = '', $vItemConfig = '') {
        if ($vStepAlias != '') {
            $oParentStep = $this->_instantiateStep($vStepAlias, $vJobAlias);
            $oParentJob = $oParentStep->getParentJob();
        } else {
            $oParentStep = new Varien_Object();
            $oParentJob = new Varien_Object();
        }

        if ($vItemConfig == '') {
            $oTaskConfig = new Varien_Object();
        } else {
            $oTaskConfig = Mage::getModel('core/config_element', $vItemConfig);
        }

        $aItemTaskParams = array(
            'test', // Task Code
            $oTaskConfig, // Task config
            $oParentJob, // Parent Job
            $oParentStep // Parent Step
        );

        $oItemTask = Mage::getModel($vAlias, $aItemTaskParams);
        $oItemTask->setLogger($this->_instantiateMockLogger());

        return $oItemTask;
    }


    /**
     * Returns a mock of the batchjob/logger class used for logging.
     *
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _instantiateMockLogger() {
        $oLoggerMock = $this->getModelMock('batchjob/logger', ['log'], false, ['test', 6]);
        $oLoggerMock->expects($this->any())
            ->method('log')
            ->will($this->returnSelf());

        return $oLoggerMock;
    }


}