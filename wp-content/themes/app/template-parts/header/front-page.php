<?php

$app = app\App::getInstance();
$option = $app->getOptions()->get('start_header');

echo $option;