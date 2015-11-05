<?php


/**
 * Base class which should be implemented for local file and SFTP file transports. 
 */
abstract class Aligent_Batchjob_Model_Transport_Abstract extends Varien_Object {
    
    /**
     * Returns true if a remote server is ready to receive a file.
     * 
     * @return boolean True if ready to export. 
     */
    abstract public function isRemoteReady();


    /**
     * Returns true if an import file (specified by $fileType) exists on the server.
     * 
     * @param string $fileType  File type indicator
     * @return boolean TRUE if file exists.
     */
    abstract public function importFileExists($fileType);
    

    /**
     * Retrieves the import file (specified by $fileType) from the server
     * and stores it in a local temporary location.
     * Returns the name of the temporary file.  
     * This is essentially a noop for local storage. 
     *
     * @param string $fileType  File type indicator
     * @return string The name of the local file once retreived.
     */
    abstract public function fetchImportFile($fileType);
    
    /**
     * Stores the named file in a local archive folder for safe keeping.
     * 
     * @param string $vLocalFileName The name of the file to archive.
     * @return void
     */
    public function archiveFile($vLocalFileName) {
        if ($this->getReallyArchive()) {
            $archivePath = $this->getArchivePath();
            if (!file_exists($archivePath)) {
                mkdir($archivePath);
            }
            if (!is_dir($archivePath)) {
                throw new Mage_Exception(sprintf("Archive path '%s' exists, but is not a directory.", $archivePath));
            }
            if (!is_writable($archivePath)) {
                throw new Mage_Exception(sprintf("Archive path '%s' is not writable.", $archivePath));
            }
            rename($vLocalFileName, $archivePath . basename($vLocalFileName));
        }
    }
    
    protected static function _getFileSpecForFileType($fileType) {
        // TODO return prefix and sufgfix for each fule type here.
        return array('prefix' => $prefix, 'suffix' => $suffix);
    }

}