<?php

require_once __DIR__ . '/../vendor/autoload.php';

use USPC\Feeds\FeedsConnector;
use USPC\Feeds\ServiceConnection;

$sc = new ServiceConnection(__DIR__ . '/feeds-config.php');
$fc = new FeedsConnector();
