<?php

namespace Parser;

use Client\IHttpClient;
use FilterLinks\ICleanLinks;
use Prepare\IPrepareOutput;

abstract class ParserRoutine
{
    public $client;
    public $filter;
    public $output;
    public $conn;

    /**
     * @param IHttpClient $client
     */
    public function setHttpClient(IHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param ICleanLinks $cleanLinks
     */
    public function setCleanLinks(ICleanLinks $cleanLinks)
    {
        $this->filter = $cleanLinks;
    }

    /**
     * @param IPrepareOutput $output
     */
    public function setOutput(IPrepareOutput $output)
    {
        $this->output = $output;
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** InsertDB  */
    public function insertDB($sql)
    {
        $this->conn->execInsert($sql);
    }

    /** SelectDB  */
    public function selectDB($sql)
    {
        return $this->conn->execSelect($sql);
    }

    /** Clean table  */
    public function cleanTable($nameTable)
    {
        $this->conn->cleanTable($nameTable);
    }

}