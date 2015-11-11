<?php

/**
 * Simple class to assist with generating CSV files.  Use setHeader to define an
 * array of key/valuer pairs describing the CSV file.  The key sets the internal
 * name of the field, while the value defines the name oif the field in the header
 * row of the CSV.  Use of keys in this way allows the row data to be supplied to
 * writeDataRow in any order (or additional fields to be supplied), and the class
 * will ensure that only the correct fields are always written in the correct
 * order in the CSV.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 *
 * @method setDelimiter(string $delimiter)
 * @method getDelimiter() string
 * @method setEnclosure(string $enclosure)
 * @method getEnclosure() string
 * @method setHeader(array $header)
 * @method getHeader() array
 * @method setStreamWriter(Varien_Io_File $header)
 * @method getStreamWriter() Varien_Io_File
 */
class Aligent_Batchjob_Model_Simplefilewriter extends Varien_Object {

    public function __construct() {
        parent::__construct();

        $this->setDelimiter(',')
            ->setEnclosure('"');
    }

    /**
     * Writes the header row to the stream writer.
     *
     * @return $this For chaining
     */
    public function writeHeaderRow() {
        $this->getStreamWriter()->streamWriteCsv(array_values($this->getHeader()), $this->getDelimiter(), $this->getEnclosure());
        return $this;
    }


    /**
     * Writes a data row to the stream writer.  The header array is used to
     * ensure that the correct fields are written in the correct order.
     *
     * @param $aData
     * @return $this
     */
    public function writeDataRow($aData) {
        $aRow = array();
        foreach ($this->getHeader() as $vKey => $vTitle) {
            if (array_key_exists($vKey, $aData)) {
                $aRow[] = $aData[$vKey];
            } else {
                $aRow[] = '';
            }
        }
        $this->getStreamWriter()->streamWriteCsv($aRow, $this->getDelimiter(), $this->getEnclosure());
        return $this;
    }
}