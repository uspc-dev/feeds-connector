<?php

require_once __DIR__ . '/../setup.php';

$_POST['action'] = 'remove-merchants';
$_POST['merchants_id'] = [
    19, // 101Phones | Commission Junction
    21, // 110 Athletics | eBay Enterprise Network
    23, // 11Main | Commission Junction
];


$html = $fc->process();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Feeds</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style>
        .nav-tabs, form {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?= $html ?>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.3.min.js" integrity="sha256-aaODHAgvwQW1bFOGXMeX+pC4PZIPsvn2h1sArYOhgXQ=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>    
</body>
</html>
