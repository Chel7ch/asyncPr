<?php

namespace Client;

use DiDom\Document;

class HttpCurl implements IHttpClient
{
    use ErrResp, SaveHTMLPage;
    public $url;
    private $header;
    private $page;
    public $scratch;

    public function __construct($url, $scratch, $header)
    {
        $this->url = $url;
        $this->scratch = $scratch; // !!!!!!!!!!!!!!!!!!!!!!!
        $this->header = $header;
    }

    public function getPage($page)
    {
        $postData = '';
         $agent = null ;
        $this->page = $page;
        $outputString = 1;
        $cookie = COOKIE_FILE. '/cookie.txt';

        print_r($postData);
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $page);

        curl_setopt($curl, CURLOPT_POST, !is_null($postData)); // TRUE для HTTP POST
        if ($postData) curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); // сами POST переменые

        curl_setopt($curl, CURLOPT_HEADER, 0);  // FALSE отключение заголовкa из вывода
        curl_setopt($curl, CURLOPT_NOBODY, 0);  // TRUE исключения тела ответа из вывода
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);  // TRUE для подробного отчета при неудаче, если полученный HTTP-код больше или равен 400.
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1); // для curl_info() TRUE для отслеживания строки запроса дескриптора.
        curl_setopt($curl, CURLOPT_FILETIME, 1); // для  curl_info() попытка получения даты модификации удаленного документа

        // HTTPHEADER приорететнее USERAGENT , REFERER , ENCODING  и прочих. Формат  array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        // curl_setopt($curl, CURLOPT_HTTPHEADER,['X-Requested-With: XMLHttpRequest', 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3']);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent); // HTTPHEADER приорететнее USERAGENT
        curl_setopt($curl, CURLOPT_ENCODING, 'utf-8'); // Содержимое заголовка "Accept-Encoding:
        curl_setopt($curl, CURLOPT_REFERER, 'http://diesel.elcat.kg/'); // Содерж. заг-а "Referer:" -URL с какой страницы пришли

        //curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:8080"); // IP HTTP-прокси, через который будут направляться запросы.
        //curl_setopt($curl, CURLOPT_PROXYTYPE, "CURLPROXY_SOCKS5"); // либо либо CURLPROXY_SOCKS4, CURLPROXY_SOCKS5


        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  // FALSE запрет редиректов
        // работают при CURLOPT_FOLLOWLOCATION = TRUE:
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);  // Максимальное количество принимаемых редиректов
        curl_setopt($curl, CURLOPT_POSTREDIR, 2);  // 1 (301 Moved Permanently), 2 (302 Found) и 4 (303 See Other), задают должен ли метод HTTP POST обрабатываться , если произошел указанный тип перенаправления.
        curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, 1);  // TRUE для продолжения посылки логина и пароля при редиректах, даже при изменении имени хоста.

        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);  // файл, куда пишутся куки
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);  // файл, откуда читаются куки
        curl_setopt($curl, CURLOPT_COOKIESESSION, 1); // TRUE для указания текущему сеансу начать новую "сессию" cookies. т.е. игнорирует все "сессионные" cookies, полученные из предыдущей сессии.

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // FALSE для остановки cURL от проверки сертификата узла сети
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // FALSE  не проверяем SSL удалённого сервера. 0-1-2

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $outputString); // 0 - вывод в браузер 1- возвращение строки
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);  // Макс. позволенное колич. секунд для выполнения cURL-функций
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);  // сек. таймаут соединения. 0 - бесконечное ожидание

        try {
            $content = curl_exec($curl);
                $this->errResp(http_response_code(), $page);

                $this->curlInfo($curl, $postData);

        } catch (RequestException $e) {
        }
        curl_close($curl);
        if (isset($content) && !empty($content)) {
            $document = new Document($content);
            $links = $document->find('a::attr(href)');

            $this->SaveHTMLPage($document ,$page);

//            $this->benefit($page, $document, $scratch);
        } else $links[] = $this->url;

        return $links;
    }

    public function curlInfo($dp, $postData = '')
    {
        // Информация о последней операции
        $info = curl_getinfo($dp);
//        echo  '<br>CURLINFO_HTTP_CONNECTCODE'.curl_getinfo($dp, CURLINFO_HTTP_CONNECTCODE);
//        echo  '<br>CURLINFO_HTTPAUTH_AVAIL'.curl_getinfo($dp, CURLINFO_HTTPAUTH_AVAIL);
//        echo  '<br>CURLINFO_PROXYAUTH_AVAIL'.curl_getinfo($dp, CURLINFO_PROXYAUTH_AVAIL);
//        echo  '<br>CURLINFO_OS_ERRNO'.curl_getinfo($dp, CURLINFO_OS_ERRNO);
        echo '<br><pre>';
        printf('<br>&nbsp&nbsp TOTAL_TIME: &nbsp %.3f seconds', $info['total_time']);
        printf('<br> CONNECT_TIME:&nbsp&nbsp %.3f seconds &nbsp&nbsp&nbsp&nbsp//Время затраченное на установку соединения', $info['connect_time']);
        printf('<br>&nbsp&nbsp UPLOAD: &nbsp SIZE: %.1f Kb &nbsp&nbsp', $info['size_upload'] / 1024);
        printf('SPEED: %.1f Kb/sec', $info['size_download'] / 1024);
        printf('<br> DOWNLOAD: &nbsp SIZE: %.1f Kb &nbsp&nbsp', $info['size_download'] / 1024);
        printf('SPEED: %.1f Kb/sec', $info['speed_download'] / 1024);
        echo '<br> LOCAL_IP ' . $info['local_ip'] . '&nbsp&nbsp&nbsp&nbsp&nbspPORT: ' . $info['local_port'];
        echo '<br> PRIMARY_IP ' . $info['primary_ip'] . '&nbsp&nbsp&nbsp&nbsp&nbspPORT: ' . $info['primary_port'];
        echo '<br>';
        echo '<br> URL: &nbsp&nbsp&nbsp' . $info['url'];
        echo '<br> CONTENT_TYPE:&nbsp&nbsp&nbsp' . $info['content_type'];
        echo '<br> RESPONSE_CODE: ' . $info['http_code'];
        echo '<br>';
        echo '<br> __HEADER_OUT__:<br>' . $info['request_header'];

        if ($postData) {
            echo ' __$postData__<br>';
            print_r($postData);
            echo '<br>';
        }
        echo '__RESPONSE_HEADER__<br>';
        $headers = get_headers($this->page);
        foreach ($headers as $header) {
            echo $header . '<br>';
        }
        echo '<br>';

        if (curl_getinfo($dp, CURLINFO_FILETIME != -1)) {
            echo '<br> FILETIME' . curl_getinfo($dp, CURLINFO_FILETIME) . '// серверная дата загруженного документа';
        }
        if ($info['redirect_count'] != 0) {
            echo '<br> REDIRECT_COUNT:&nbsp&nbsp&nbsp' . $info['redirect_count'];
            echo '<br> REDIRECT_TIME: &nbsp&nbsp' . $info['redirect_time'] . ' seconds';
            echo '<br> REDIRECT_URL: &nbsp&nbsp' . $info['redirect_url'];
        }
        echo '</pre>';
    }

    public function postPage($page,$postData)
    {
    }

    public function close()
    {
    }
}