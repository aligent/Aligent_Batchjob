<?php

class Aligent_Batchjob_Model_Transport_Local extends Aligent_Batchjob_Model_Transport_Abstract {

    /**
     * Initialise the class with the parameters it will need to function.  Normally these parameters 
     * will come from system configuration and be assigned by the factory.
     * 
     * @param string $vFetchPath The folder containing the files to import.
     * @param string $vArchivePath The folder into which files will be archived after import.
     * @param boolean $bReallyArchive Whether to actually archive files or not.
     * @return \Aligent_Batchjob_Model_Transport_Local Initialised local transport.
     */
    public function init($vFetchPath, $vArchivePath, $bReallyArchive = true) {

        $this->setFetchPath((substr($vFetchPath, -1, 1) == '/') ? $vFetchPath : $vFetchPath.'/');
        $this->setArchivePath((substr($vArchivePath, -1, 1) == '/') ? $vArchivePath : $vArchivePath.'/');
        $this->setReallyArchive($bReallyArchive);
        return $this;
    }

    /**
     * Returns true if a remote server is ready to receive a file.
     * 
     * @return boolean True if ready to export. 
     */
    public function isRemoteReady() {
        return true;
    }


    /**
     * Return a boolean indicating whether any files match a given filespec (which may include wildcards).
     * 
     * @param string $vFileSpec The file name (which may include wildcards) to match.
     * @return boolean True if a file matches, false if not
     */
    protected function _fileExists($vFileSpec) {
        $aFiles = glob($this->getFetchPath().$vFileSpec);
        return $aFiles !== false && count($aFiles) > 0;
    }

    /**
     * Return the full path and filename for the first file matching a given filespec (which may include wildcards).
     * 
     * @param string $vFileSpec The file name (which may include wildcards) to match.
     * @return string Full path and filename. 
     */
    protected function _fetchFile($vFileSpec) {
        $aFiles = glob($this->getFetchPath().$vFileSpec);
        sort($aFiles);
        return array_shift($aFiles);
    }

    private static function _fileSpecToGlob(array $filespec) {
        return $filespec['prefix'] . '*' . $filespec['suffix'];
    }


    /**
     * Returns true if an import file (specified by $fileType) exists on the server.
     *
     * @param string $fileType  File type indicator
     * @return boolean TRUE if file exists.
     */
    public function importFileExists($fileType) {
        return $this->_fileExists(self::_fileSpecToGlob(self::_getFileSpecForFileType($fileType)));
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
        return $this->_fetchFile(self::_fileSpecToGlob(self::_getFileSpecForFileType($fileType)));
    }

}