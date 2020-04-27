<?php

namespace Proxy;

use Client\IHttpClient;
use Config\Config;

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


    /**
     * @param int $multiRequest
     * @param mixed $proxy
     * @return array  IP addresses
     */
    public function getGroupPages($multiRequest, $proxy = '')
    {
        $urls = array_fill(0, $multiRequest, self::CHECKER);
        return $this->client->getGroupPages($urls, $proxy);
    }

    /** definition own Ip */
    public function ownIp()
    {
        Config::set('proxyOn', 0);
        $ip = join($this->getGroupPages(1,''));
        preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip, $match);
        self::$ownIp = join($match);
        Config::set('proxyOn', 1);
    }

    /**
     * compares received list proxy with your real ip address
     * @param array $listProxy
     * @return array good proxy
     */
    public function diffIP($listProxy)
    {
        $ip = array();

        $pr = $this->getGroupPages(Config::get('multiRequest'),$listProxy);

        foreach ($pr as $key => $proxy) {
            if (empty($proxy)) {
                echo '<br>Плохой proxy ' . $listProxy[$key] . ' Нет ответа.';
                continue;

            } elseif (strlen($proxy) < 50) {
                preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $proxy, $match);
                $receiveIp = implode('', $match);

                if ($receiveIp and $receiveIp != self::$ownIp) {
                    $ip[] = $listProxy[$key];

                    echo '<br><br>+++++++  ' . $listProxy[$key] . '________________' . $receiveIp . '<br>';//!!!!!!!!

                } elseif ($receiveIp and $receiveIp == self::$ownIp) {
                    echo '<br>Ответ: ' . $receiveIp . ' Вместо ' . @$listProxy[$key] . ' . Прозрачный прокси.';//!!!!!!
                } else echo '<br>Плохой proxy ' . $listProxy[$key] . ' Отказ.';
            } else echo '<br>Плохой proxy ' . $listProxy[$key] . ' Отказ';
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

        while ($goodProxy < Config::get('countGoodProxy') or Config::get('countGoodProxy') == -1) {

            while ($selProxy) {
                $listProxy = array_splice($selProxy, 0, Config::get('multiRequest'));
                Config::set('workProxy', $listProxy);
                $good = (array)$this->diffIP(Config::get('workProxy'));

                if (!empty($good)) $this->insertProxy($good);

                $cycle = $cycle + Config::get('multiRequest');
                $goodProxy = $goodProxy + count($good);
            }

            echo '<br><br> Проверенно: ' . $cycle . ' proxy. Найденно: ' . $goodProxy . '<br>';//!!!!!!!!!!!

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
     * @param string $tab tab name
     * @return int number of entries in the table
     */
    public function selectCount($tab = 'collect_proxy')
    {
        $query = 'SELECT COUNT(field1) FROM ' . $tab;

        return (integer)join($this->conn->execSelect($query));
    }

    /**
     * @param array $proxy
     * @param string $tab tab name
     */
    public function insertProxy($proxy, $tab = 'check_proxy')
    {
        if (Config::get('saveGoodProxyInDB') == 1) $this->saveInDB($proxy);
        else $this->saveInFile($proxy);
    }

    /**
     * saves a list of good proxies in the file
     * @param array $proxy
     */
    public function saveInFile($proxy)
    {
        $fd = fopen(Config::get('goodProxyFile'), 'a');
        foreach ($proxy as $pr) {
            fputs($fd, $pr . PHP_EOL);
        }
        fclose($fd);
    }

    /**
     * saves a list of good proxies in the table
     * @param array $proxy
     * @param string $tab
     */
    public function saveInDB($proxy, $tab = 'check_proxy')
    {
        $query = 'INSERT INTO ' . $tab . ' (field1) VALUES';
        foreach ($proxy as $pr) {
            $query .= '(\'' . $pr . '\'),';
        }
        $query = substr($query, 0, -1) . ';';

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

    /** @param IHttpClient $client */
    public function setHttpClient(IHttpClient $client)
    {
        $this->client = $client;
    }

}