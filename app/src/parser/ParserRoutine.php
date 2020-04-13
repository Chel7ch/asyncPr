<?php

namespace Parser;

abstract class ParserRoutine
{
    /**
     * @param \Client\IHttpClient $client
     */
    public function setHttpClient(\Client\IHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param \FilterLinks\ICleanLinks $cleanLinks
     */
    public function setCleanLinks(\FilterLinks\ICleanLinks $cleanLinks)
    {
        $this->filter = $cleanLinks;
    }

    /**
     * @param \Prepare\IPrepareOutput $output
     */
    public function setOutput(\Prepare\IPrepareOutput $output)
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