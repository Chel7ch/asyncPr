<?php
return array(
    'http' =>
        array(
            'curl' => 'Client\HttpCurl',
            'guzzle' => 'Client\HttpGuzzle',
            'webDriver' => 'Client\HttpPHPWebDriver',
            'proxyWebDriver' => 'Client\ProxyPHPWebDriver',
        ),
    'filterlinks' =>
        array(
            'empty' => 'FilterLinks\NoCleanLinks',
            'main' => 'FilterLinks\MainCleanLinks',
            'paginator' => 'FilterLinks\PaginatorCleanLinks',
            'url_links_tail' => 'FilterLinks\URLLinksTailCleanLinks',
            'url_tail_links' => 'FilterLinks\URLTailLinksCleanLinks',
        ),
    'output' =>
        array(
            'turn' => 'Prepare\TurnOverOutput',
            'straight' => 'Prepare\StraightOutput',
            'hidemy' => 'Prepare\PrHidemyName',
        ),
    'store' =>
        array(
            'mysql' => 'DB\MYSQLConnection',
            'sqlite' => 'DB\SQLiteConnection)',
        ),
);