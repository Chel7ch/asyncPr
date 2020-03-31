<?php

namespace Client;

use Proxy\CookingProxy;

class HttpCurl implements IHttpClient
{
    use LogErrorResponse, SaveHTMLPage;

    public $url;
    private $header;
    private $page;
    public $scratch;

    public function __construct($url, $header)
    {
        $this->url = $url;
        $this->header = $header;
    }

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
        print_r($proxy);
        echo '__________$proxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

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
            $this->curlInfo($resp);
            @$this->errorResponse($resp['http_code'], $urls[$i], $proxy[$i]);

            curl_close($conn[$i]);
            $this->saveHTMLPage($content[$i], $urls[$i]);
        }
        curl_multi_close($mh);

        return $content;

    }

    public function curlInfo($resp)
    {
        if (CURL_HTTP_INFO == 1) {
            echo '<br>' . $resp['http_code'] . ' ответ сервера';
            echo '<br>' . $resp['total_time'] . ' total_time';
            echo '<br>' . $resp['connect_time'] . ' Время затраченное на установку соединения<br>';
//          HTTPInfo::Info($page, $curl);
        }
    }

    /**
     * @param string $page
     * @param string $proxy
     * @return false|resource
     */
    public function setoptCurl($page, $proxy = '')
    {
        $postData = '';
        $agent = null;
        $outputString = 1;
        $followlacation = 1;
        $cookie = COOKIE_FILE . '/cookie.txt';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $page);
//        if ($postData != '') {
//            curl_setopt($curl, CURLOPT_POST, !is_null($postData)); // TRUE для HTTP POST
//            if ($postData) curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); // сами POST переменые
//        }
//        curl_setopt($curl, CURLOPT_HEADER, 1);  // FALSE отключение заголовкa из вывода
//        curl_setopt($curl, CURLOPT_NOBODY, 0);  // TRUE исключения тела ответа из вывода
//        curl_setopt($curl, CURLOPT_FAILONERROR, 1);  // TRUE для подробного отчета при неудаче, если полученный HTTP-код больше или равен 400.
//        curl_setopt($curl, CURLINFO_HEADER_OUT, 1); // для curl_info() TRUE для отслеживания строки запроса дескриптора.
//        curl_setopt($curl, CURLOPT_FILETIME, 1); // для  curl_info() попытка получения даты модификации удаленного документа

        // HTTPHEADER приорететнее USERAGENT , REFERER , ENCODING  и прочих. Формат  array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
//         curl_setopt($curl, CURLOPT_HTTPHEADER,['X-Requested-With: XMLHttpRequest', 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3']);
//        curl_setopt($curl, CURLOPT_USERAGENT, $agent); // HTTPHEADER приорететнее USERAGENT
        curl_setopt($curl, CURLOPT_ENCODING, 'utf-8'); // Содержимое заголовка "Accept-Encoding:
        curl_setopt($curl, CURLOPT_REFERER, 'http://diesel.elcat.kg/'); // Содерж. заг-а "Referer:" -URL с какой страницы пришли

        curl_setopt($curl, CURLOPT_PROXY, $proxy); // IP HTTP-прокси, через который будут направляться запросы.
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 0); //
        curl_setopt($curl, CURLOPT_PROXYTYPE, "CURLPROXY_SOCKS4"); // либо либо CURLPROXY_SOCKS4, CURLPROXY_SOCKS5


        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $followlacation);  // FALSE запрет редиректов
        // работают при CURLOPT_FOLLOWLOCATION = TRUE:
//        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);  // Максимальное количество принимаемых редиректов
//        curl_setopt($curl, CURLOPT_POSTREDIR, 2);  // 1 (301 Moved Permanently), 2 (302 Found) и 4 (303 See Other), задают должен ли метод HTTP POST обрабатываться , если произошел указанный тип перенаправления.
//        curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, 1);  // TRUE для продолжения посылки логина и пароля при редиректах, даже при изменении имени хоста.

        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);  // файл, куда пишутся куки
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);  // файл, откуда читаются куки
//        curl_setopt($curl, CURLOPT_COOKIESESSION, 1); // TRUE для указания текущему сеансу начать новую "сессию" cookies. т.е. игнорирует все "сессионные" cookies, полученные из предыдущей сессии.

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // FALSE для остановки cURL от проверки сертификата узла сети
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // FALSE  не проверяем SSL удалённого сервера. 0-1-2

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $outputString); // 0 - вывод в браузер 1- возвращение строки
        curl_setopt($curl, CURLOPT_TIMEOUT, CURL_TIMEOUT);  // Макс. позволенное колич. секунд для выполнения cURL-функций
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, CURL_CONNECTTIMEOUT);  // сек. таймаут соединения. 0 - бесконечное ожидание

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

        $curl = $this->setoptCurl($page, $proxy);
        try {
            $content = curl_exec($curl);

            $resp = curl_getinfo($curl);
            $this->curlInfo($resp);

            $this->errorResponse($resp['http_code'], $page, $proxy);

        } catch (RequestException $e) {
        }
        curl_close($curl);

        $this->saveHTMLPage($content, $page);

        return $content;

    }

    public function postPage($page, $postData)
    {
    }


}