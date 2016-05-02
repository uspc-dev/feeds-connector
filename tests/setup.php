<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/store-info.php';

use USPC\Feeds\FeedsConnector;
use USPC\Feeds\ServiceConnection;

$store = new test\StoreInfo();

$sc = new ServiceConnection(__DIR__ . '/feeds-config.php');
$fc = new FeedsConnector($sc, $store);

