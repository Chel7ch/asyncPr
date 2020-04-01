<?php

namespace Proxy;

use Client\IHttpClient;

class ProxyChecker
{
    const REQUEST_AT_TIME = 50;
    private static $goodProxy = 0;
    /** @var IHttpClient */
    private $client;
    /** url  for checking proxy */
    const CHECKER = 'http://razrabotkaweb.ru/ip.php';
//    const CHECKER = 'http://httpbin.org/ip';

    /** @param IHttpClient $client */
    public function setHttpClient(IHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $proxy
     * @return string you IP address
     */
    public function getPage($proxy = '')
    {
        return $this->client->getPage(self::CHECKER, $proxy);
    }

    /**
     * compares your real ip address with the received one
     * @param string $proxy
     * @return void
     */
    public function diffIP($proxy = '')
    {
        static $own = 0;
        static $ownIp = '';
        static $cycle = 1;

        if ($own == 0) {
            $ip = $this->getPage();
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip, $match);
            $ownIp = implode('', $match);
            $own++;
        }

        $pr = $this->getPage($proxy);

        if (strlen($pr) < 60) {
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $pr, $match);
            $receiveIp = implode('', $match);

            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $proxy, $match);//!!!!!
            $proxyIp = implode('', $match);//!!!!

            echo '<br> Проверенно: ' . ++$cycle . ' proxy. Найденно: ' . self::$goodProxy . '<br>';//!!!!!!!!!!!

            if ($receiveIp and $receiveIp != $ownIp) {
                $this->insertProxy($proxy);
                self::$goodProxy++;

                echo '<br>' . $proxy . ' goodProxy+++++++++++++<br>';//!!!!!

            } elseif ($receiveIp and $receiveIp == $ownIp) {
                echo '<br>' . $receiveIp . ' ответ, вместо ' . $proxyIp;//!!!!!!
                echo ' Прозрачный прокси.----<br>';
            }
        } else echo ' Плохой proxy: ' . $proxy . ' Нет ответа.';
    }

    /** checking the list proxy  */
    public function rollingProxy()
    {
        $rowId = 1;

        $countProxy = $this->selectCount();
        $selProxy = $this->selectProxy($rowId);

        while (self::$goodProxy < COUNT_GOOD_PROXY or COUNT_GOOD_PROXY == -1) {

            foreach ($selProxy as $proxy) {
                $this->diffIP($proxy);
            }
            $rowId += self::REQUEST_AT_TIME;

            if ($rowId >= $countProxy) {
                $countProxy = $this->selectCount();
                if ($rowId >= $countProxy) break;
            }
            $selProxy = $this->selectProxy($rowId);
        }
    }

    /**
     * @param int $rowId
     * @param string $tab
     * @return array
     */
    public function selectProxy($rowId, $tab = 'collect_proxy')
    {
        $query = 'SELECT  field1 FROM ' . $tab . ' WHERE id >' . $rowId . ' LIMIT ' . self::REQUEST_AT_TIME;

        return $this->conn->execSelect($query);
    }

    /**
     * @param string $tab
     * @return int
     */
    public function selectCount($tab = 'collect_proxy')
    {
        $query = 'SELECT COUNT(field1) FROM ' . $tab;

        $count = $this->conn->execSelect($query);
        return (integer)join($count);
    }

    /**
     * @param string $proxy
     * @param string $tab
     */
    public function insertProxy($proxy, $tab = 'check_proxy')
    {
        $query = 'INSERT INTO ' . $tab . ' (field1) VALUES(\'' . $proxy . '\')';
        $this->conn->execInsert($query);
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** cleanTable
     * @param string $nameTable
     */
    public function cleanTable($nameTable = 'check_proxy')
    {
        $this->conn->cleanTable($nameTable);
    }
}
