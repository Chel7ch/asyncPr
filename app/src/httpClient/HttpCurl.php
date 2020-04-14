<?php

namespace Client;

use Config\Config;
use Exception;
use Proxy\CookingProxy;

class HttpCurl implements IHttpClient
{
    use LogErrorResponse, SaveHTMLPage;

    /**
     * @param array $urls
     * @param mixed $proxy
     * @return array
     */
    public function getGroupPages($urls, $proxy = '')
    {
        $content = array();
        $mh = curl_multi_init();

        if (!empty(CookingProxy::$workProxy)) $proxy = CookingProxy::$workProxy;
//        print_r($proxy);
//        echo '__________$proxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        if (is_array($proxy)) {
            foreach ($urls as $i => $url) {
                $conn[$i] = $this->setoptCurl($url, $proxy[$i]);
                curl_multi_add_handle($mh, $conn[$i]);
            }
        } elseif ($proxy == '') {
            foreach ($urls as $i => $url) {
                $conn[$i] = $this->setoptCurl($url);
                curl_multi_add_handle($mh, $conn[$i]);
            }
        } else exit('Сurl input parameters are invalid');


        do {
            curl_multi_exec($mh, $active);
        } while ($active);

        for ($i = 0; $i < count($urls); $i++) {
            $content[$i] = curl_multi_getcontent($conn[$i]);
            curl_multi_remove_handle($mh, $conn[$i]);

            $resp = curl_getinfo($conn[$i]);
            $this->curlInfo($resp,$url, $conn[$i]);
            @$this->errorResponse($resp['http_code'], $urls[$i], $proxy[$i]);

            curl_close($conn[$i]);
            $this->saveHTMLPage($content[$i], $urls[$i]);
        }
        curl_multi_close($mh);

        return $content;

    }

    public function curlInfo($resp, $url, $curl)
    {
        if (Config::get('curlHTTPInfo') >= 1) {
            echo '<br>' . $resp['http_code'] . ' ответ сервера';
            echo '<br>' . $resp['total_time'] . ' total_time';
            echo '<br>' . $resp['connect_time'] . ' Время затраченное на установку соединения';
            if (Config::get('curlHTTPInfo') == 2) HTTPInfo::Info($url, $curl);
        }
    }

    /**
     * @param string $page
     * @param string $proxy
     * @return false|resource
     */
    public function setoptCurl($page, $proxy = '')
    {
        $outputString = 1;
        $followlacation = 1;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $page);
//        if (Config::get('postData')) {
//            curl_setopt($curl, CURLOPT_POST, 1); // TRUE для HTTP POST
//            curl_setopt($curl, CURLOPT_POSTFIELDS, Config::get('postData')); // сами POST переменые
//        }
//        curl_setopt($curl, CURLOPT_HEADER, 1);  // FALSE отключение заголовкa из вывода
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1); //true вывод сформированного request заголовкa в curl_info

        // HTTPHEADER приорететнее USERAGENT , REFERER , ENCODING  и прочих. Формат  array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt($curl, CURLOPT_HTTPHEADER, Config::get('header'));
        curl_setopt($curl, CURLOPT_USERAGENT, Config::get('userAgent')); // HTTPHEADER приорететнее USERAGENT
        curl_setopt($curl, CURLOPT_ENCODING, 'utf-8'); // Содержимое заголовка "Accept-Encoding:
        curl_setopt($curl, CURLOPT_REFERER, Config::get('referer')); // Содерж. заг-а "Referer:" -URL с какой страницы пришли

        curl_setopt($curl, CURLOPT_PROXY, $proxy); // IP HTTP-прокси, через который будут направляться запросы.
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 1); //
        curl_setopt($curl, CURLOPT_PROXYTYPE, "CURLPROXY_SOCKS4"); // либо либо CURLPROXY_SOCKS4, CURLPROXY_SOCKS5

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $followlacation);  // FALSE запрет редиректов

        curl_setopt($curl, CURLOPT_COOKIEJAR, Config::get('cookieFile'));  // файл, куда пишутся куки
        curl_setopt($curl, CURLOPT_COOKIEFILE, Config::get('cookieFile'));  // файл, откуда читаются куки
//        curl_setopt($curl, CURLOPT_COOKIESESSION, 1); // TRUE для указания текущему сеансу начать новую "сессию" cookies. т.е. игнорирует все "сессионные" cookies, полученные из предыдущей сессии.

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // FALSE для остановки cURL от проверки сертификата узла сети
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // FALSE  не проверяем SSL удалённого сервера. 0-1-2

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $outputString); // 0 - вывод в браузер 1- возвращение строки
        curl_setopt($curl, CURLOPT_TIMEOUT, Config::get('respTimeout'));  // Макс. позволенное колич. секунд для выполнения cURL-функций
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, Config::get('connentTimeout'));  // сек. таймаут соединения. 0 - бесконечное ожидание

        return $curl;

    }

    /**
     * @param string $page
     * @param string $proxy
     * @return bool|string
     */
    public function getPage($page, $proxy = '')
    {
        $content = '';
        if (!empty(CookingProxy::$workProxy)) $proxy = join(CookingProxy::$workProxy);
//        echo '__________getPage curl___________________<br>';
//        print_r($proxy);
//        echo 'curl__________$proxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//        print_r(CookingProxy::$workProxy);
//        echo 'curl__________$workProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//        print_r(CookingProxy::$listProxy);
//        echo 'curl__________$listProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//        print_r(CookingProxy::$badProxy);
//        echo 'curl__________$badProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $curl = $this->setoptCurl($page);
        try {
            $content = curl_exec($curl);

            $resp = curl_getinfo($curl);
            $this->curlInfo($resp, $page, $curl);

            $this->errorResponse($resp['http_code'], $page, $proxy);

            if (!$curl) throw new Exception('Curl not found');

        } catch (Exception $e) {
            echo 'Curl: ' . $e->getMessage();
        }

        curl_close($curl);

        $this->saveHTMLPage($content, $page);

        return $content;

    }

    public function postPage($page, $postData)
    {
        $curl = $this->setoptCurl($page);
            curl_setopt($curl, CURLOPT_POST, 1); // TRUE для HTTP POST
            curl_setopt($curl, CURLOPT_POSTFIELDS, Config::get('postData')); // сами POST переменые
        try {
            $content = curl_exec($curl);

            $resp = curl_getinfo($curl);
            $this->curlInfo($resp, $page, $curl);

            if (!Config::get('postData')) throw new Exception('POST data not found');

        } catch (Exception $e) {
            echo 'Curl: ' . $e->getMessage();
        }

        curl_close($curl);

        $this->saveHTMLPage($content, $page);

        return $content;
    }


}