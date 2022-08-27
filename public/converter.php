<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
$start_time = microtime(true);
$client = new CoinGeckoClient();
$cache = new Memcache;
$cache->connect('127.0.0.1', 11211)
?>
    <a href="./converter.php">Converter</a>
    <a href="./price_list.php">Price List</a>
    <br>
    <br>
    <form action="./converter.php" method="post">
        <?php if (isset($_POST['input'])) {
            echo '<input type="text" id="input" name="input" value="'.$_POST['input'].'">';
        } else {
            echo '<input type="text" id="input" name="input">';
        }; ?>
        <button type="submit">Convert</button><br>
        <label for="input">
            Пример: <small>2 btc in usd</small><br>
            Если название криптовалюты состоит из нескольких слов - пишите их через "-"
        </label><br>
    </form>
<?php
$words = [];
$token = '';
$coin_name = '';
if (!empty($_POST['input'])) {
    if ($_POST['input'] === 'course_list') {
        echo 'Данное слово зарезервировано!';
        exit;
    }
    if ($cache->get($_POST['input']) !== false) {
        $result = explode('/', $cache->get($_POST['input']));
        echo 'Итого: '. $result[0] .' ('.$result[1].')';
        $end_time = microtime(true);
        echo '<br> Взяли из кеша! Время выполнения скрипта: '.round($end_time - $start_time, 4) .' сек.';
        exit;
    } else {
        echo 'Кеш просрочился, загружаем новый курс, ожидайте...<br>';
    }
    $words = explode(' ', $_POST['input']);
} else {
    echo 'Пусто!';
    exit;
}

if (intval($words[0]) === 0) {
    echo 'Первое слово должно быть целым числом и больше нуля!!!!';
    exit;
} elseif (!isset($words[2]) || $words[2] !== 'in') {
    echo 'Третье слово должно быть "in"!';
    exit;
} elseif ($words[3] !== 'usd' && $words[3] !== 'rub') {
    echo 'Поддерживаются только доллар и рубль (usd, rub)!';
    exit;
}

try {
    $words[1] = implode(' ', explode('-', $words[1]));
    $data = $client->search()->getSearch($words[1]);
    if (isset($data['coins']) && isset($data['coins'][0])) {
        $token = $data['coins'][0]['id'];
        $coin_name = $data['coins'][0]['name'];
        echo 'Расчет стоимости '.$coin_name.'...';
    } else {
        echo 'К сожалению такого токена не найдено, попробуйте написать название по другому';
    }
} catch (Exception $e) {
    var_dump($e);
}

try {
    $data = $client->simple()->getPrice($token, $words[3]);
    $price = $data[$token][$words[3]];
    $result = $price * $words[0];
    echo '<br> Итого: '.$result;
    $end_time = microtime(true);
    echo '<br> Время выполнения скрипта: '.round($end_time - $start_time, 4) .' сек.';
    $cache->set($_POST['input'], $result.'/'.$coin_name, false, 60);
} catch (Exception $e) {
    var_dump($e);
}