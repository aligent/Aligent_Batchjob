<?php

/**
 * Factory for creating Job objects.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Job_Factory {
    const XML_PATH_JOB = "batchJobs/{jobCode}";
    const XML_PATH_EMAIL_LOG_LEVEL = 'system/email_logs/level';

    protected $_aJobs = array();

    public function getJob($vJobCode) {
        if (!array_key_exists($vJobCode, $this->_aJobs)) {
            $oJobConfig = $this->_getJobConfig($vJobCode);
            $vJobModelAlias = $oJobConfig->model;
            if ((string) $vJobModelAlias == "") {
                $vJobModelAlias = 'batchjob/job_generic';
            }

            $oJobModel = Mage::getModel($vJobModelAlias, array($vJobCode, $oJobConfig));

            $vJobLoggerAlias = $oJobConfig->logModel;
            if ((string) $vJobLoggerAlias == "") {
                $vJobLoggerAlias = 'batchjob/logger';
            }
            $oJobModel->setLogger(Mage::getModel($vJobLoggerAlias, [$vJobCode, Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_LEVEL)]));

            $this->_aJobs[$vJobCode] = $oJobModel;
        }
        return $this->_aJobs[$vJobCode];
    }

    protected function _getJobConfig($vJobCode) {
        $vJobPath = str_replace("{jobCode}", $vJobCode, self::XML_PATH_JOB);

        return Mage::getConfig()->getNode($vJobPath);
    }
}