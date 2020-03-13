<?php

namespace Client;


class HttpCurl implements IHttpClient
{
    use ErrResp, SaveHTMLPage;
    public $url;
    private $header;
    private $page;
    public $scratch;

    public function __construct($url, $header)
    {
        $this->url = $url;
        $this->header = $header;
    }

    public function getMultiPages($urls, $proxy = '')
    {

        $mh = curl_multi_init();

        foreach ($urls as $i => $url) {
            $conn[$i] = $this->setoptCurl($url);

            curl_multi_add_handle($mh, $conn[$i]);
        }

        do {
            curl_multi_exec($mh, $active);
        } while ($active); //Пока все соединения не отработают

        for ($i = 0; $i < count($urls); $i++) {
            $result[$i] = curl_multi_getcontent($conn[$i]);
            curl_multi_remove_handle($mh, $conn[$i]);

            $resp = curl_getinfo($conn[$i]);
            $this->errResp($resp['http_code'], $urls[$i]);

//            if (HTTP_INFO == 1) HTTPInfo::Info($page, $conn[$i]);
            curl_close($conn[$i]);
        }

        curl_multi_close($mh);

        return $result;


    }

    public function setoptCurl($page, $proxy = '')
    {
        $postData = '';
        $agent = null;
        $outputString = 1;
        $followlacation = 1;
        $cookie = COOKIE_FILE . '/cookie.txt';
//        print_r($proxy);
//        echo 'dd<br>';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $page);
//        if ($postData != '') {
//            curl_setopt($curl, CURLOPT_POST, !is_null($postData)); // TRUE для HTTP POST
//            if ($postData) curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); // сами POST переменые
//        }
        curl_setopt($curl, CURLOPT_HEADER, 1);  // FALSE отключение заголовкa из вывода
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

//        curl_setopt($curl, CURLOPT_PROXY, '77.37.208.119:55491'); // IP HTTP-прокси, через который будут направляться запросы.
        curl_setopt($curl, CURLOPT_PROXY, $proxy); // IP HTTP-прокси, через который будут направляться запросы.
//        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 1); //
//        curl_setopt($curl, CURLOPT_PROXYTYPE, "CURLPROXY_SOCKS4"); // либо либо CURLPROXY_SOCKS4, CURLPROXY_SOCKS5


        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  // FALSE запрет редиректов
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
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);  // Макс. позволенное колич. секунд для выполнения cURL-функций
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);  // сек. таймаут соединения. 0 - бесконечное ожидание

        return $curl;

    }

    public function getPage($page, $proxy = '')
    {
        $content = '';
        $curl = $this->setoptCurl($page, $proxy);
        try {
            $content = curl_exec($curl);

            $resp = curl_getinfo($curl);
            $this->errResp($resp['http_code'], $page);

            echo '<br>' . $resp['http_code'] . 'ответ сервера<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//            if (HTTP_INFO == 1) HTTPInfo::Info($page, $curl);

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