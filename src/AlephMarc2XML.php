<?php

/**
 * This class can convert an MARC output from Aleph to a XML file
 *
 * @license http://opensource.org/licenses/MIT MIT License (MIT)
 * @copyright Copyright (c) 2015, Swiss Social Archives
 */
class AlephMarc2XML {

    /**
     * @var SimpleXMLElement
     */
    private $records;

    /**
     * @param $filename
     * @throws Exception
     */
    public function __construct($filename)
    {
        $this->records = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><collection />');

        $rawRecords = $this->readRecordsFromFile($filename);

        foreach($rawRecords as $rawRecord) {
            $this->processRawRecord($rawRecord);
        }

    }

    /**
     * @return SimpleXMLElement
     */
    public function get()
    {
        return $this->records;
    }


    /**
     * @param $filename
     * @return array
     * @throws Exception
     */
    private function readRecordsFromFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception('Input file does not exist¨!');
        }

        $input = file_get_contents($filename);
        if(empty($input)) {
            throw new Exception('Input file is empty!');
        }
        $input = str_replace(PHP_EOL.'      ', PHP_EOL, $input);
        $rawRecords = explode(PHP_EOL.'*****'.PHP_EOL.'Dokument', $input);
        if(count($rawRecords) < 2) {
            throw new Exception('No records in unput file!!');
        }

        // amount of record test
        $intro = $rawRecords[0];
        $tmpArray = explode('Number of Records: ', $intro);
        $tmpArray = explode(PHP_EOL, $tmpArray[1]);
        $amountOfRecords = intval($tmpArray[0]);
        unset($rawRecords[0]);
        if(count($rawRecords) != $amountOfRecords) {
            throw new Exception('The amount of records in the header and in file is not the same!');
        }

        return $rawRecords;
    }

    /**
     * @param $rawRecord
     */
    private function processRawRecord($rawRecord)
    {

        $record = $this->records->addChild('record');

        foreach(explode(PHP_EOL.PHP_EOL, $rawRecord) as $rawField) {
            $rawFieldLines = explode(PHP_EOL, $rawField);
            if (count($rawFieldLines) < 2) {
                continue;
            }

            // add sub fields
            if( substr($rawFieldLines[1], 0, 1) == '|') {
                // create data field
                $dataField = $record->addChild('field');
                $dataField->addAttribute('key', $rawFieldLines[0]);
                unset($rawFieldLines[0]);
                $valueLine = implode('', $rawFieldLines);


                $subFieldsArray = explode('|', substr($valueLine, 1));
                foreach ($subFieldsArray as $rawSubField) {
                    $rawSubFieldArray = explode(' ', $rawSubField);
                    $key = $rawSubFieldArray[0];

                    $value = '';
                    if (isset($rawSubFieldArray[1])) {
                        unset($rawSubFieldArray[0]);
                        $value = trim(implode(' ', $rawSubFieldArray));
                    }

                    $subField = $dataField->addChild('subfield', $this->escapeForXML($value));
                    $subField->addAttribute('key', $key);

                }
            } else {
                // create data field
                $controlField  = $record->addChild('field', $this->escapeForXML($rawFieldLines[1]));
                $controlField->addAttribute('key', $rawFieldLines[0]);

            }

        }
    }

    /**
     * @param $input
     * @return mixed
     */
    private function escapeForXML($input)
    {
        return str_replace('&', '&amp;', $input);
    }

}