<?php
/**
 * Generic job that simply iterates through all of it's child steps, repeating
 * until a step aborts the job..
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Job_Generic extends Aligent_Batchjob_Model_Job_Abstract {

    const XML_PATH_EMAIL_TO = 'system/email_logs/email';

    /**
     * Call to initiate processing of a job.
     *
     * @return void
     */
    public function run() {
        $this->getLogger()->log(str_repeat("=", 80), Zend_Log::INFO);
        $this->getLogger()->log($this->_vJobCode." job started.", Zend_Log::INFO);

        try {
            do {
                $bContinue = false;

                foreach ($this->_oJobConfig->steps->children() as $vStepCode => $oStepConfig) {
                    $this->getLogger()->log("Step $vStepCode starting...", Zend_Log::DEBUG, true);
                    $bContinue = $this->_getStep($vStepCode)->run();
                    $this->getLogger()->log("Step $vStepCode complete.", Zend_Log::DEBUG, true);

                    if ($bContinue === false) {
                        $this->getLogger()->log("Job aborted by $vStepCode step.", Zend_Log::INFO);
                        break;
                    }
                }
            } while ($bContinue !== false);
            $this->getLogger()->log($this->_vJobCode . " job completed.", Zend_Log::INFO);
        } catch (Exception $e) {
            $this->getLogger()->log("Exception occurred processing ".$this->_vJobCode." job.  Message: ".$e->getMessage(), Zend_Log::ERR);
        }

        $this->_emailLog();
    }


    /**
     * Email the log entries to the nominated email addresses.
     */
    public function _emailLog() {
        $vTo = Mage::getStoreConfig(self::XML_PATH_EMAIL_TO);
        if (trim($vTo) !== '') {
            $vBody = $this->getLogger()->getLogToEmail();
            if (trim($vBody) != '') {
                try {
                    $oEmail = Mage::getModel('core/email')
                        ->setBody($vBody)
                        ->setSubject("Log messages from job: " . $this->_vJobCode)
                        ->setToEmail(explode(',', $vTo))
                        ->send();
                } catch (Zend_Mail_Transport_Exception $e) {
                    $this->getLogger()->log("Exception occurred sending email logs: ".$e->getMessage(), Zend_Log::EMERG);
                }
            }
        }
    }
}