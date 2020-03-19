<?php

namespace Proxy;

use DB\DBPdoCRUD;

class ProxyChecker
{
    public $goodProxy = array();

    /**
     * @param \Client\IHttpClient $client
     */
    public function setHttpClient(\Client\IHttpClient $client)
    {
        $this->client = $client;
//        $this->url = $this->client->url;
    }


    /**
     * @param string $url
     * @return string HTML doc
     */
    public function getPage($url, $proxy = '')
    {
        return $this->client->getPage($url, $proxy);
    }

    public function ownIP($proxy = '', $url = 'http://razrabotkaweb.ru/ip.php')
//    public function ownIP($proxy = '', $url = 'http://httpbin.org/ip')
    {
        return $this->client->getPage($url, $proxy);
    }

    public function diffIP($proxy = '', $url = 'http://razrabotkaweb.ru/ip.php')
//    public function diffIP($proxy = '', $url = 'http://httpbin.org/ip')
    {
        static $own = 0;
        static $ownIp = '';

        if ($own == 0) {
            $ip = $this->ownIP();
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip, $match);
            $ownIp = implode('', $match);
            $own++;
        }

        $pr = $this->client->getPage($url, $proxy);

        if (!empty($pr)) {
            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $pr, $match);
            $receiveIp = implode('', $match);

            preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $proxy, $match);
            $proxyIp = implode('', $match);
            echo '<br>' . $receiveIp . ' ответ прокси вместо ' . $proxyIp;//!!!!!!!!!!!!!!!!!!!!

            if ($receiveIp and $receiveIp != $ownIp) {
                $this->goodProxy[] = $proxy;
                echo $proxy .'++++++<br>';
            }
        } else echo 'плохой proxy' . $proxy;

        return $pr;
    }

    public function checkProxy()
    {
        $rowId = 1;
        $z =1;

        $selProxy = $this->selDBProxy($rowId);

        while (count($this->goodProxy) < 50) {

            foreach ($selProxy as $proxy) {
                $this->diffIP($proxy);

                echo '<br>' . $z++ . ' цикл checkProxy<br>';//!!!!!!!!!!!
            }
            $rowId += 50;
            if ($rowId > 2500) break;

            print_r($this->goodProxy);//!!!!!!!!!!!!!!!!!!!!!!
            echo 'всего<br>';//!!!!!!!!!!!!!!!!!!!!!!

            $selProxy = $this->selDBProxy($rowId);
        }

    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** SelectDB  */
    public function selDBProxy($rowId)
    {
        return $this->conn->selDBProxy($rowId);
    }

}
