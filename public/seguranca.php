<?php
include_once(__DIR__.'/../models/Seguranca.php');

use \models\Seguranca;

$seguranca = new Seguranca();

if (!$seguranca->usuarioLogado()) {
    header("Location: ".BASE."login");
    exit;
}