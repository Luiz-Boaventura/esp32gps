<?php
include_once('seguranca.php'); 
include_once(__DIR__.'/../../../../models/Dispositivo.php');

use \models\Dispositivo;

$_SESSION['home_mensagem'] = true;

$dispositivo_id = intval($url[count($url)-1]);

if ($dispositivo_id === 0 || $dispositivo_id === $_SESSION['dispositivo']['id']) {
    $_SESSION['mensagem']['erro'] = 'Erro! Parâmetros incorretos';
    header('Location: '.BASE);
    exit;
}

$dispositivo = new Dispositivo();

if ($dispositivo->trocarDispositivoUsuario($_SESSION['usuario']['id'], $dispositivo_id)) {
    unset($_SESSION['area_config']);
    $_SESSION['mensagem']['sucesso'] = 'Sucesso! Dispositivo de rastreio foi alterado.';
    header('Location: '.BASE);
    exit;
} else {
    $_SESSION['mensagem']['erro'] = 'Erro! Não foi possível trocar de dispositivo, tente novamente.';
    header('Location: '.BASE);
    exit;
}