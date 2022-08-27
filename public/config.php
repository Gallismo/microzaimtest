<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
$client = new CoinGeckoClient();
$cache = new Memcache;
$cache->connect('127.0.0.1', 11211);