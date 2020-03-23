<?php

namespace Proxy;

class CookingProxy
{
    static public $listProxy = array();
    static public $badProxy = array();
    static public $multiRequest;
    private $conn;

    public function cook($count = MULTI_REQUEST)
    {
        self::$listProxy = $this->selectProxy();

        if (count(self::$listProxy) >= $count) self::$multiRequest = $count;
        else self::$multiRequest = count(self::$listProxy);

        return array_splice(self::$listProxy, 0, self::$multiRequest);
    }

    public function replace($oldProxy, $badPr)
    {
        self::$badProxy[] = $badPr;
        $new = array_shift(self::$listProxy);
        $key = array_search($badPr, $oldProxy);

        if ($new) {
            $oldProxy[$key] = $new;
        } else {
            $listProxy = $this->selectProxy();
            self::$listProxy = array_diff($listProxy, self::$badProxy, $oldProxy);

            // если менять плохой прокси нечем, то уменьшаем группу прокси на 1
            if (empty(self::$listProxy)) {
                unset($oldProxy[$key]);
                $oldProxy = array_values($oldProxy);
                self::$multiRequest--;
            }
            // если проксей не осталось , то отключаем прокси
            if (count($oldProxy) == 0) {
                $oldProxy = (string)'';
                self::$multiRequest = 1;
            }
        }

        return $oldProxy;
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /**
     * @return string
     */
    public function prepareSelect()
    {
        return 'SELECT  field1 FROM  check_proxy';
    }

    /** SelectDB
     * @return array
     */
    public function selectProxy()
    {
        $query = $this->prepareSelect();
        return $this->conn->execSelect($query);
    }

}