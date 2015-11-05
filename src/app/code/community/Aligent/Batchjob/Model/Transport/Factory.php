<?php


class Aligent_Batchjob_Model_Transport_Factory {
    const CONFIG_TRANSPORT =        'system/{{config_set}}/transport';
    const CONFIG_SFTP_HOSTNAME =    'system/{{config_set}}/sftp_hostname';
    const CONFIG_SFTP_USERNAME =    'system/{{config_set}}/sftp_username';
    const CONFIG_SFTP_PASSWORD =    'system/{{config_set}}/sftp_password';
    const CONFIG_SFTP_FETCH_PATH =  'system/{{config_set}}/sftp_fetch_path';
    const CONFIG_LOCAL_FETCH_PATH = 'system/{{config_set}}/local_fetch_path';
    const CONFIG_ARCHIVE_PATH =     'system/{{config_set}}/archive_path';

    /**
     * Returns a correctly configured transport object based on system configuration
     * parameters.
     * 
     * @param Aligent_Batchjob_Model_Logger $oLogger The logger to use for all transport operations.
     * @param string $vConfigSet One of the CONFIG_SET_* constants to identify which config values should be used.
     * @return Aligent_Batchjob_Model_Transport_Abstract
     */
    static function getTransport($oLogger, $vConfigSet) {
        switch (self::_getStoreConfig($vConfigSet, self::CONFIG_TRANSPORT)) {
            case Aligent_Batchjob_Model_System_Config_Source_Transport::TRANSPORT_SFTP:
                $aConfig = array(
                    'host' => self::_getStoreConfig($vConfigSet, self::CONFIG_SFTP_HOSTNAME),
                    'username' => self::_getStoreConfig($vConfigSet, self::CONFIG_SFTP_USERNAME),
                    'password' => Mage::helper('core')->decrypt(self::_getStoreConfig($vConfigSet, self::CONFIG_SFTP_PASSWORD)),
                    'fetch_path' => self::_getStoreConfig($vConfigSet, self::CONFIG_SFTP_FETCH_PATH),
                    'archive_path' => self::_getAbsolutePath(self::_getStoreConfig($vConfigSet, self::CONFIG_ARCHIVE_PATH)),
                );
                return Mage::getModel('batchjob/transport_sftp')->init($aConfig)->setLogger($oLogger);
                break;
            case Aligent_Batchjob_Model_System_Config_Source_Transport::TRANSPORT_LOCAL:
                $vFetchDir   = self::_getAbsolutePath(self::_getStoreConfig($vConfigSet, self::CONFIG_LOCAL_FETCH_PATH));
                $vArchiveDir = self::_getAbsolutePath(self::_getStoreConfig($vConfigSet, self::CONFIG_ARCHIVE_PATH));
                return Mage::getModel('batchjob/transport_local')->init($vFetchDir, $vArchiveDir)->setLogger($oLogger);
                break;
        }
    }
    
    
    /**
     * Turn relative paths into absolute paths based on the magento base directory.
     * 
     * @param string $vPath Either a relative or absolute path.
     * @return string The absolute path 
     */
    static private function _getAbsolutePath($vPath) {
        if (substr($vPath, -1, 1) != '/') {
            $vPath .= '/';
        }
        
        if (substr($vPath, 0, 1) != '/') {
            $vBaseDir = Mage::getBaseDir();
            $vPath = $vBaseDir . '/' . $vPath;
        }
        return $vPath;
    }


    /**
     * Get a system config value from the appropriate set of SFTP settings.
     *
     * @param string $vConfigSet Name of the group of settings to fetch from.
     * @param string $vMageConfigPath Magento config path the "{{config_set}} placeholder will be replaced with the actual config set value.
     * @return mixed Magento config value
     */
    static private function _getStoreConfig($vConfigSet, $vMageConfigPath) {
        $vConfigPath = str_replace('{{config_set}}', $vConfigSet, $vMageConfigPath);
        $vConfigValue = Mage::getStoreConfig($vConfigPath);
        return $vConfigValue;
    }
}
