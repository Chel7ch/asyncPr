<?php

namespace Proxy;

use Client\IHttpClient;

class ProxyGroupChecker
{
    private static $ownIp = '';
    /** @var IHttpClient */
    private $client;
    /** request a proxy at a time from DB */
    const REQUEST_AT_TIME = 50;
    /** url  for checking proxy */
    const CHECKER = 'http://razrabotkaweb.ru/ip.php';
//    const CHECKER = 'http://httpbin.org/ip';

    /** @param IHttpClient $client */
    public function setHttpClient(IHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param mixed $proxy
     * @return array  IP addresses
     */
    public function getGroupPages($proxy = '', $request = MULTI_REQUEST)
    {
        $urls = array_fill(0, $request, self::CHECKER);
        return $this->client->getGroupPages($urls, $proxy);
    }

    /**
     * @param void
     * @return void
     */
    public function ownIp()
    {
        $ip = join($this->getGroupPages('', 1));

        preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip, $match);
        self::$ownIp = join($match);
    }

    /**
     * compares received list with your real ip address
     * @param array $listProxy
     * @return array
     */
    public function diffIP($listProxy)
    {
        $ip = array();

        $pr = $this->getGroupPages($listProxy);

        foreach ($pr as $key => $proxy) {
            if (empty($proxy)) {
                echo '<br>Плохой proxy '. $listProxy[$key] .' Нет ответа.';
                continue;

            } elseif (strlen($proxy) < 50) {
                preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $proxy, $match);
                $receiveIp = implode('', $match);

                if ($receiveIp and $receiveIp != self::$ownIp) {
                    $ip[] = $listProxy[$key];

                    echo '<br><br>+++++++  ' . $listProxy[$key] . '________________'.$receiveIp.'<br>';//!!!!!!!!

                } elseif ($receiveIp and $receiveIp == self::$ownIp) {
                    echo '<br>Ответ: ' . $receiveIp . ' Вместо ' . $listProxy[$key] . ' . Прозрачный прокси.';//!!!!!!
                }else echo '<br>Плохой proxy '.$listProxy[$key].' Отказ.';
            } else echo '<br>Плохой proxy '.$listProxy[$key].' Отказ';
        }

        return $ip;
    }

    /** checking the list proxy  */
    public function rollingProxy()
    {
        $rowId = 1;
        $cycle = 0;
        $goodProxy = 0;

        $this->ownIp();
        $countProxy = $this->selectCount();
        $selProxy = $this->selectProxy($rowId);

        while ($goodProxy < COUNT_GOOD_PROXY or COUNT_GOOD_PROXY == -1) {

            while ($selProxy) {
                $listProxy = array_splice($selProxy, 0, MULTI_REQUEST);

                $good = (array)$this->diffIP($listProxy);

                if (!empty($good)) $this->insertProxy($good);

                $cycle = $cycle + MULTI_REQUEST;//!!!!!!!!!!!
                $goodProxy = $goodProxy + count($good);//!!!!!!!!!!!
            }

            echo '<br>Проверенно: ' . $cycle . ' proxy. Найденно: ' . $goodProxy . '<br>';//!!!!!!!!!!!

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

        return (integer)join($this->conn->execSelect($query));
    }

    /**
     * @param array $proxy
     * @param string $tab
     */
    public function insertProxy($proxy, $tab = 'check_proxy')
    {
        $query = 'INSERT INTO ' . $tab . ' (field1) VALUES';
        foreach ($proxy as $pr) {
            $query .= '(\'' . $pr . '\'),';
        }
        $query = substr($query, 0, -1) . ';';
        echo '<br>'.$query;//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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