<?php

class Aligent_Batchjob_Model_Transport_Sftp extends Aligent_Batchjob_Model_Transport_Abstract {

    /**
     * SFTP configuration, using dummy values as default
     *
     */
    public $aFtpConfig = array(
        'host' => 'localhost',
        'username' => 'user',
        'password' => 'pass',
        'fetch_path' => 'path/to/the/files/',
        'really_archive' => true, // Whether to actually archive the files
    );

    private $_tempImportDir = '';

    /**
     * The FTP client object.
     * @var Varien_Io_Sftp
     */
    private $_oSftp = NULL;
    private $_dirs = array();

    /*
     * Prepare the SFTP Adapter, allow config overrides
     */

    public function init($aConfig = null) {
        if (!is_null($aConfig)) {
            $this->aFtpConfig = array_merge($this->aFtpConfig, $aConfig);
        }

        // Parts of this code expect these params to be stored Varien_Object style as 
        // they are in the local transport.  So we'll extract them from the array and 
        // store them appropriately here.
        $this->setReallyArchive($this->aFtpConfig['really_archive']);
        $vArchivePath = $this->aFtpConfig['archive_path'];
        $this->setArchivePath((substr($vArchivePath, -1, 1) == '/') ? $vArchivePath : $vArchivePath.'/');

        $this->_oSftp = new Aligent_Batchjob_Model_Transport_Variensftp();
        
        set_error_handler(
                function($iErrNo, $vErrString, $vErrFile, $iErrLine) { 
                    restore_error_handler();
                    throw new Mage_Exception("User Notice Occurred: ($iErrNo) $vErrString in line $iErrLine of $vErrFile.");
                }, E_USER_NOTICE);
        
        $this->_oSftp->open($this->aFtpConfig);
        
        restore_error_handler();
        
        return $this;
    }

    
    /**
     * Returns true if a remote server is ready to receive a file.
     * 
     * @return boolean True if ready to export. 
     */
    public function isRemoteReady() {
        $result = $this->_pushd($this->aFtpConfig['send_path']);
        $result = ($result && (($count = count($ls = $this->_oSftp->ls())) == 2)); //includes dot files
        $this->_popd();
        return $result;
    }



    /**
     * Returns true if an import file (specified by $fileType) exists on the server.
     *
     * @param string $fileType  File type indicator
     * @return boolean TRUE if file exists.
     */
    public function importFileExists($fileType) {
        return FALSE !== $this->_getImportFilename($fileType);
    }
    
    private function getTempImportDir() {
        if (!$this->_tempImportDir) {
            $this->_tempImportDir = Mage::getBaseDir('var') . '/sftptmp/';
        }
        return $this->_tempImportDir;
    }

    private function _fetchImportFile($remoteFilename) {
        $success = TRUE;
        $localFilename = FALSE;
        // Check whether the directory exist, create it if not
        if (!file_exists($this->getTempImportDir())) {
            $success = ($success && mkdir($this->getTempImportDir(), 0777, TRUE));
        }
        // Make sure the directory is a directory (not a file) and is writable
        $success = ($success && is_dir($this->getTempImportDir()) && is_writable($this->getTempImportDir()));
        // Test the remote filename
        $success = ($success && FALSE !== $remoteFilename);
        // Create a temporary file on the local system
        $success = ($success && FALSE !== ($localFilename = tempnam($this->getTempImportDir(), $remoteFilename)));
        // #70 File permissions
        // tempnam creates a file with 0600 perms, so need to fix that
        $success = ($success && FALSE !== (chmod($localFilename, 0664))); // ug=rw,o=r
        $this->getLogger()->log('Fetching remote file: '.$remoteFilename.' to: '.$localFilename, Zend_Log::INFO);
        // cd into the directory and get the file, saving it to a local temp file
        // success must come second here to make sure pushd is called to match the popd that happens later.
        $success = ($this->_pushd($this->aFtpConfig['fetch_path']) && $success);
        $success = ($success && FALSE !== $this->_oSftp->read($remoteFilename, $localFilename));

        if (FALSE !== $remoteFilename && FALSE === $this->_oSftp->rm($remoteFilename)) {
            $this->getLogger()->log('Error deleting remote file: '.$remoteFilename.' from remote working directory: '.$this->_oSftp->pwd(), Zend_Log::ERR);
            $this->getLogger()->log('Underlying error messages: '.$this->_oSftp->getErrors(), Zend_Log::ERR);
            throw new Exception('Unable to delete remote file '.$remoteFilename.' Underlying errors: '.$this->_oSftp->getErrors());
        } else {
            $this->getLogger()->log('Deleted remote file: '.$remoteFilename, Zend_Log::NOTICE);
        }


        // If there was an error, but the tempfile had been created
        if (!$success && !!$localFilename) {
            unlink($localFilename); // delete the file
            $localFilename = FALSE;
        }
        $this->_popd();
        return $localFilename;
    }
    

    /**
     * Retrieves the import file (specified by $fileType) from the server
     * and stores it in a local temporary location.
     * Returns the name of the temporary file.  
     * This is essentially a noop for local storage. 
     *
     * @param string $fileType  File type indicator
     * @return string The name of the local file once retreived.
     */
    public function fetchImportFile($fileType) {
        $vLocalFilename = $this->_fetchImportFile($this->_getImportFilename($fileType));
        return $vLocalFilename;
    }

    /**
     * @param string $fileType  File type indicator
     * @return boolean|string  FALSE if there was an error or the file was not found
     *                         otherwise returns the name of the file.
     */
    private function _getImportFilename($fileType) {
        $aFiles = $this->_getAvailableImportFilenames($fileType);
        if (count($aFiles) > 0) {
            asort($aFiles);
            return array_shift($aFiles);
        } else {
            return false;
        }
        
    }
    
    
    /**
     * @param string $fileType  File type indicator
     * @return array  Returns an array of matching filenames.
     */
    private function _getAvailableImportFilenames($fileType) {
        $filespec = self::_getFileSpecForFileType($fileType);
        $result = $this->_pushd($this->aFtpConfig['fetch_path']);
        $result = ($result && FALSE !== ($ls = $this->_oSftp->ls()));
        $this->_popd();
        
        // only look for the file if $result is not already FALSE (i.e. if no errors have occurred)
        if (FALSE !== $result) {
            $aFiles = array();
            array_walk($ls,
                function($aElement) use ($filespec, &$aFiles) {
                    if (strpos($aElement['text'], $filespec['prefix']) === 0
                        && strpos($aElement['text'], $filespec['suffix']) === strlen($aElement['text']) - strlen($filespec['suffix'])) {    
                        return $aFiles[] = $aElement['text'];
                    }
                });
            return $aFiles;
        }
        
        return array();
    }

    /**
     * pushd and popd should always be called in pairs. even if pusd returns FALSE
     * @param string $dir   The name of the dir to push
     * @return boolean      Whether or not the directory was changed successfully
     */
    private function _pushd($dir) {
        $result = (FALSE !== ($oldCwd = $this->_oSftp->pwd()));
        $result = ($this->_oSftp->cd($dir));
        array_push($this->_dirs, $oldCwd);
        return $result;
    }

    /**
     * pushd and popd should always be called in pairs. even if pushd returns FALSE
     * @return boolean      Whether or not the directory was changed successfully
     */
    private function _popd() {
        $oldCwd = array_pop($this->_dirs);
        if (FALSE !== $oldCwd) {
            return $this->_oSftp->cd($oldCwd);
        }
        return FALSE;
    }

}
