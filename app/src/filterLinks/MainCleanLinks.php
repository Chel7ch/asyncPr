<?php

namespace FilterLinks;

use Config\Config;

/** Removes unnecessary links from the set */

class MainCleanLinks implements ICleanLinks
{
    /**
     * @param array $links
     * @return array
     */
    public function cleanLinks($links)
    {
        $link = array();

        foreach ($links as $urn) {

            $urn = urldecode(trim($urn));

            // убираем якоря
            if (stristr($urn, '#')) {
                $urn = stristr($urn, '#', true);
            }
            if ($urn == '') continue;

            // убираем ссылки на картинки и проч файлы
            if (stristr($urn, '.')) {
                if (strpos($urn, Config::get('project')))
                    $t = str_replace(Config::get('project'), '', stristr($urn, Config::get('project')));
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

            $urlHost = parse_url($urn, PHP_URL_HOST);

            // добавляем протокол HTTP или HTTPS
            if (substr($urn, 0, 2) == '//') {
                if ($_SERVER['HTTPS'] && $urlHost == Config::get('project')) {
                    $link[] = 'https:' . $urn;
                    continue;
                } elseif ($_SERVER['REQUEST_SCHEME'] && $urlHost == Config::get('project')) {
                    $link[] = 'http:' . $urn;
                    continue;
                }//удаляем ссылки на  поддомены
                elseif (stristr($urlHost, Config::get('project'))) continue;
                // удаляем ссылки на сторонние сайты
                elseif (!stristr($urlHost, Config::get('project'))) continue;
            }

            //  если абсолютная ссылка
            if (parse_url($urn, PHP_URL_SCHEME)) {
                // сохраняем абсолютную ссылку
                if ($urlHost == Config::get('project')) {
                    $link[] = $urn;
                    continue;
                } //удаляем ссылки на  поддомены
                elseif (stristr($urlHost, Config::get('project'))) continue;
                // удаляем ссылки на сторонние сайты
                elseif (!stristr($urlHost, Config::get('project'))) continue;
            }

            // убираем ссылку на почтовый адрес
            if (stristr($urn, 'mailto:')) continue;

            // создаем  URI
            $link[] = Config::get('url') . $urn;
        }

        if (!empty($link)) $link = array_values(array_unique($link));

        return $link;
    }

}