<?php

namespace FilterLinks;

use Config\Config;

class MainCleanLinks implements ICleanLinks
{
    public function cleanLinks($links)
    {
        $link = array();
        $strlenProject = strlen(Config::get('project'));

        foreach ($links as $urn) {

            $urn = urldecode(trim($urn));

//            echo '<br>'. $urn. '<br>';
//            print_r(parse_url($urn));


            // убираем якоря
            if (stristr($urn, '#')) {
                $urn = stristr($urn, '#', true);
            }
            if ($urn == '') continue;

            // убираем ссылки на картинки и проч файлы
            if (stristr($urn, '.')) {
                if (strpos($urn, Config::get('project'))) $t = str_replace(Config::get('project'), '', stristr($urn, Config::get('project')));
                else $t = $urn;
                if (stristr($t, '.') && !preg_match('#\.(php|html|htm|)?[\? \/]#', $t)) continue;
            }

            //убираем двойной слэш на конце
            if (substr($urn, -2) == '//') {
                $urn = substr($urn, 0, -1);
            }
            // добавляем одинарный слэш на конце
            if (substr($urn, -1) != '/') {
                $urn = $urn . '/';
            }

            // добавляем протокол HTTP или HTTPS
            if (substr($urn, 0, 2) == '//') {
                if (isset($_SERVER['HTTPS']) && substr($urn, 2, $strlenProject) == Config::get('project')) {
                    $link[] = 'https:' . $urn;
                    continue;
                } elseif (isset($_SERVER['REQUEST_SCHEME']) && substr($urn, 2, $strlenProject) == Config::get('project')) {
                    $link[] = 'http:' . $urn;
                    continue;
                }// удаляем ссылки на сторонние сайты
                else {
                    // echo '!!!Внимание удаляется ссылка  '. $urn . '  Ты уветен???<br>';
                    continue;
                }
            }
            //  если абсолютная ссылка
            if (substr($urn, 0, 7) == 'http://' || substr($urn, 0, 8) == 'https://') {

                $t = array("https://" => "", "http://" => "");
                $t = strtr("$urn", $t);
                // сохраняем абсолютную ссылку
                // неполная проверка надо добавить проверку последнего/следующего символа PROJECT
                if (substr($t, 0, $strlenProject) == Config::get('project')) {
                    $link[] = $urn;
                    continue;
                } // удаляем ссылки на сторонние сайты
                elseif (!stristr($t, Config::get('project')))
                    continue;
                //удаляем ссылки на  поддомены
                elseif (stristr($t, Config::get('project'))) {
                    //echo '!!!Внимание удаляется ссылка  '. $urn . '  Ты уветен???<br>';
                    continue;
                }
            }

            // убираем ссылку на почтовый адрес
            if (stristr($urn, 'mailto:'))
                continue;

            // создаем  URI
            $link[] = Config::get('url') . $urn;

        }
        if (!empty($link)) $link = array_values(array_unique($link));

        return $link;
    }
}