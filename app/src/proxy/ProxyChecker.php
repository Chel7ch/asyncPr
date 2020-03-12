<?php

namespace Proxy;


class ProxyChecker
{
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
    public function getPage($url)
    {
        return $this->client->getPage($url);
    }

    public function ownIP($url = 'http://razrabotkaweb.ru/ip.php')
    {
        return $this->client->getPage($url);
    }

    public function diffIP($proxy, $url = 'http://razrabotkaweb.ru/ip.php')
    {
        static $own = 0;
        static $ownIp = '';
        if ($own == 0) {
            $ownIp = $this->ownIP();
            $own++;
        }

        $proxy = $this->client->getPage($url);
        if (!strpos($proxy, $ownIp)) {
            echo $ownIp;
            return $proxy;
        }
        return 'плохой proxy' . $proxy . '<br>';
    }

    public function checkProxy()
    {
        foreach ($this->selDBProxy() as $proxy) {
            $this->diffIP($proxy);
        }
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** SelectDB  */
    public function selDBProxy()
    {
        return $this->conn->selDBProxy();
    }


    public function checkerr()
    {
        $gdata = array();
        $gproxy_a = array();
        $gproxy = "";
        $c = $g = $b = $t = '0';

        $proxy_a = file("proxylist.txt");
        $t = count($proxy_a);
        foreach ($proxy_a as $key => $value) {
            $c++;
            $value = str_replace(array("\n", "\r", " "), '', $value);
            $buf = get('http://yoip.ru/', $value);
            preg_match("#<span class='ip'>([a-z:0-9.]+)</span>#i", $buf, $gdata["ip"]);
            if (isset($gdata["ip"][1])) {
                $ip = $gdata["ip"][1];
                if (!isset($gproxy_a[$ip])) {
                    $g++;
                    $gproxy_a[$ip] = $value;
                    $gproxy .= $value . "
";
                }
                echo "[c:$c/t:$t][g:$g/b:$b] " . $value . " (" . $ip . ") - ok;\r\n";
            } else {
                $b++;
                echo "[c:$c/t:$t][g:$g/b:$b] " . $value . " - error;\r\n";

            }
        }

    }
}
