<?php

/**
 * Generic logging abstraction which is injected into each job/step/itemTask.
 * Ensures each job has it's own log file within the "batchjobs" folder in the
 * Magento var/log folder.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Logger {
    const LOG_DIR = 'batchjobs';

    protected $_vJobCode = "none";

    protected $_iEmailLogLevel = Zend_Log::INFO;
    protected $_aEmailMessages = [];
    protected $_oFormatter;

    public function __construct($aParams) {
        $this->_vJobCode = $aParams[0];
        $this->_iEmailLogLevel = $aParams[1];

        $logDirPath = Mage::getBaseDir('log') . DS . self::LOG_DIR;
        if (!file_exists($logDirPath) || !is_dir($logDirPath)) {
            mkdir($logDirPath, 750);
        }

        $vFormat = '%timestamp% %priorityName% (%priority%): %message%';
        $this->_oFormatter = new Zend_Log_Formatter_Simple($vFormat);

    }


    /**
     * @return string Path to the log file for this job.
     */
    public function getLogPath() {
        return self::LOG_DIR . '/' . $this->_vJobCode . ".log";
    }


    /**
     * Logging for import interface
     * @param string $message
     * @param int $level  ZEND_LOG log level
     * @param boolean $bDeveloperModeOnly True to log only in Developer mode
     */
    public function log($message, $level = Zend_Log::INFO, $bDeveloperModeOnly = false) {
        if ($bDeveloperModeOnly == false || ($bDeveloperModeOnly == true && Mage::getIsDeveloperMode())) {
            Mage::log($message, $level, $this->getLogPath());
        }

        if ($level <= $this->_iEmailLogLevel) {
            $this->_aEmailMessages[] = $this->_oFormatter->format(array(
                'timestamp'    => date('c'),
                'message'      => $message,
                'priority'     => $level,
                'priorityName' => $this->_getNameForLevel($level)
            ));
        }
    }


    /**
     * Returns the string representation of the log level.
     *
     * @param integer $iLevel Value from one of the Zend_Log::* constants
     * @return string String representation.
     */
    private function _getNameForLevel($iLevel) {
        switch ($iLevel) {
            case 0:
                return 'EMERG';  // Emergency: system is unusable
            case 1:
                return 'ALERT';  // Alert: action must be taken immediately
            case 2:
                return 'CRIT';   // Critical: critical conditions
            case 3:
                return 'ERR';    // Error: error conditions
            case 4:
                return 'WARN';   // Warning: warning conditions
            case 5:
                return 'NOTICE'; // Notice: normal but significant condition
            case 6:
                return 'INFO';   // Informational: informational messages
            case 7:
                return 'DEBUG';  // Debug: debug messages
        }
        return "UNKNOWN";
    }


    /**
     * Return and clear the buffer of log messages which should be emailed.
     *
     * @return string
     */
    public function getLogToEmail() {
        $vText = implode(PHP_EOL, $this->_aEmailMessages);
        $this->_aEmailMessages = [];
        return $vText;
    }

}
