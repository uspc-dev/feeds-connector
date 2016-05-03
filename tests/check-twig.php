<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader, array(
    'cache' => __DIR__ . '/compilation_cache',
));

$template = $twig->loadTemplate('index.html.twig');
echo $template->render(['name' => 'World']);