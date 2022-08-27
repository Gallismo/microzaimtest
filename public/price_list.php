<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
$start_time = microtime(true);
$client = new CoinGeckoClient();
$cache = new Memcache;
$cache->connect('127.0.0.1', 11211);
?>
<a href="./converter.php">Converter</a>
<a href="./price_list.php">Price List</a>
<br>
<br>
<?php
$json = $cache->get('WVROV01GbFhjejA9');
if ($json !== false) {
    $result = json_decode($json);
    echo 'Загружаю из кеша...<br>';
    foreach ($result as $price_str) {
        echo $price_str. "<br>";
    }
    $end_time = microtime(true);
    echo 'Время выполнения скрипта: '. round($end_time - $start_time, 4) .' сек.';
    exit;
}

echo 'Кеш просрочился, начинаю загрузку новых курсов...<br>';

try {
    $coins_list = $client->coins()->getList();
    $count = 1612;
    $result = [];
    while ($count < 1622) {
        $coin_id = $coins_list[$count]['id'];
        $coin_price = $client->simple()->getPrice($coin_id, 'rub,usd')[$coin_id];
        $price_str = $coins_list[$count]['name'] .' - '. $coin_price['rub'] .' руб. , '. $coin_price['usd'] .' USD.';
        echo $price_str;
        echo '<br>';
        $result[] = $price_str;
        $count++;
    }
    $end_time = microtime(true);
    echo 'Время выполнения скрипта: '. round($end_time - $start_time, 4) .' сек.';
    $cache->set('WVROV01GbFhjejA9', json_encode($result), false, 60);
} catch (Exception $e) {
    var_dump($e);
}

