<?php

namespace Proxy;

class ReadCollectProxy extends \Parser\ReadSaveFiles
{
    use FilterHidemyName;

    public $tail = '/proxy-list/';

    public function cleanLinks($links)
    {
        $link = array();
        foreach ($links as $urn) {
            $urn = trim($urn);

            // убираем якоря
            if (stristr($urn, '#')) {
                $urn = stristr($urn, '#', true);
            }
            if (stristr($urn, '%23')) {
                $urn = stristr($urn, '%23', true);
            }

            // если пагинатор то берем
            if (stristr($urn, $this->tail)) {
                // создаем  URN
                $link[] = $this->url . $urn;
            }
        }

        if (!empty($link)) $link = array_values(array_unique($link));

        return $link;
    }

    public function prepOutput($data)
    {
        $data = $this->specialPrepOutput($data);
        if (PREP_QUERY_FOR_DB == 1) $data = $this->prepInsertDB($data, 'collect_proxy');

        return $data;
    }

    public function prepInsertDB($data, $tabName = TAB_NAME)
    {
        $query = '';
        if(empty($data)){
            return $query;
        }

        if($tabName == TAB_NAME) $firstRow = 'INSERT INTO ' . TAB_NAME . '(links,';
        else $firstRow = 'INSERT INTO ' . $tabName . '(';

        $tab = $firstRow;
        $val = ' VALUES';
        for ($i = 1; $i < TAB_FIELDS; $i++) {
            $tab .= 'field' . ($i) . ',';
        }

        for ($i = 0; $i < count($data); $i++) {
            $val .= '(' . $data[$i] . '),';
        }

        $tab = substr($tab, 0, -1);
        $val = substr($val, 0, -1);
        $query = $tab . ')' . $val . ';';

        return $query;
    }

}
