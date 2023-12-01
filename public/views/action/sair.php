<?php
include_once('seguranca.php'); 
include_once(__DIR__.'/../../../models/Usuario.php');

use \models\Usuario;

$usuario = new Usuario();
$usuario->logout();