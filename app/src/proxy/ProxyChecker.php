<?php

namespace Proxy;

use Client\IHttpClient;

class ProxyChecker
{
    private static $goodProxy = 0;
    /** @var IHttpClient */
    private $client;
    /** url  for checking proxy */
//    const CHECKER = 'http://razrabotkaweb.ru/ip.php';
    const CHECKER = 'http://httpbin.org/ip';

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

        if ($own == 0) {
            $ip = $this->getPage();
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip, $match);
            $ownIp = implode('', $match);
            $own++;
        }

        $pr = $this->getPage($proxy);

        if (!empty($pr)) {
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $pr, $match);
            $receiveIp = implode('', $match);

            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $proxy, $match);
            $proxyIp = implode('', $match);

            echo $pr . '<br>'; //!!!!!!!!!!!!!

            if ($receiveIp and $receiveIp != $ownIp) {
                $this->insertProxy($proxy);
                self::$goodProxy++;

                echo '<br>' . $proxy . ' goodProxy+++++++++++++<br>';//!!!!!

            } elseif ($receiveIp and $receiveIp == $ownIp) {
                echo '<br>' . $receiveIp . ' ответ прокси, вместо ' . $proxyIp;
                echo ' Прозрачный прокси: ' . $proxy . ' ----<br>';
            }
        } else echo 'Плохой proxy: ' . $proxy . ' Нет ответа.';
    }

    /** checking the list proxy  */
    public function rollingProxy()
    {
        $rowId = 1;
        $z = 1;

        $selProxy = $this->selectProxy($rowId);

        while (self::$goodProxy < COUNT_GOOD_PROXY) {

            foreach ($selProxy as $proxy) {
                $this->diffIP($proxy);

                echo ' Проверенно: ' . $z++ . ' proxy. Найденно: ' . self::$goodProxy . '<br>';//!!!!!!!!!!!
            }
            $rowId += REQUEST_AT_TIME;
            if ($rowId > MAX_CHECKS) break;

            $selProxy = $this->selectProxy($rowId);
        }
    }

    /**
     * @param int $rowId
     * @param string $tab
     * @return string
     */
    public function prepareSelect($rowId, $tab = 'collect_proxy')
    {
        $sql = 'SELECT  field1 FROM ' . $tab . ' WHERE id >' . $rowId . ' LIMIT ' . REQUEST_AT_TIME;

        return $sql;
    }

    /**
     * @param string $proxy
     * @param string $tab
     * @return string
     */
    public function prepareInsertGoodProxy($proxy, $tab = 'check_proxy')
    {
        $sql = 'INSERT INTO ' . $tab . ' (field1) VALUES(\'' . $proxy . '\')';

        return $sql;
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** SelectDB
     * @param int $rowId
     * @param string $tab
     * @return array
     */
    public function selectProxy($rowId, $tab = 'collect_proxy')
    {
        $query = $this->prepareSelect($rowId, $tab = 'collect_proxy');
        return $this->conn->execSelect($query);
    }

    /** InsertDB
     * @param string $proxy
     */
    public function insertProxy($proxy)
    {
        $query = $this->prepareInsertGoodProxy($proxy);
        $this->conn->execInsert($query);
    }

    /** cleanTable
     * @param string $nameTable
     */
    public function cleanTable($nameTable = 'check_proxy')
    {
        $this->conn->cleanTable($nameTable);
    }
}
