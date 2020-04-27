<?php

namespace Proxy;

use Client\IHttpClient;
use Config\Config;

class ProxyChecker
{
    const REQUEST_AT_TIME = 50;
    private static $goodProxy = 0;
    /** @var IHttpClient */
    private $client;
    /** url  for checking proxy */
    const CHECKER = 'http://razrabotkaweb.ru/ip.php';
//    const CHECKER = 'http://httpbin.org/ip';


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
            Config::set('proxyOn', 0);
            $ip = $this->getPage();
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip, $match);
            $ownIp = implode('', $match);
            $own++;
            Config::set('proxyOn', 1);
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

        while (self::$goodProxy < Config::get('countGoodProxy') or Config::get('countGoodProxy') == -1) {

            foreach ($selProxy as $proxy) {
                Config::set('workProxy', $proxy);
                $this->diffIP(Config::get('workProxy'));

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
     * @param string $tab tab name
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

        $count = $this->conn->execSelect($query);
        return (integer)join($count);
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
        fputcsv($fd, (array)$proxy);
        fclose($fd);
    }

    /**
     * saves a list of good proxies in the table
     * @param string $proxy
     * @param string $tab tab name
     */
    public function saveInDB($proxy, $tab = 'check_proxy')
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

    /** @param IHttpClient $client */
    public function setHttpClient(IHttpClient $client)
    {
        $this->client = $client;
    }

}
