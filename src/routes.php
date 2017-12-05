<?php

$app->get('/', MyFonero\Controllers\IndexController::class . ':index');
$app->post('/registration/{login}', MyFonero\Controllers\AuthController::class . ':registration');
$app->post('/login/{login}', MyFonero\Controllers\AuthController::class . ':login');
$app->post('/send/{login}', MyFonero\Controllers\AuthController::class . ':send'); // FIXME вынести всё и разложить по полочкам
