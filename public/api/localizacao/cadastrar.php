<?php
//API SIMPLES PARA CADASTRAR OS DADOS DO ESP32
include_once(__DIR__.'/../../../models/Gps.php');
include_once(__DIR__.'/../../../models/Token.php');
include_once(__DIR__.'/../../../models/LoginDispositivo.php');

use \models\Gps;
use \models\Token;
use \models\LoginDispositivo;

$token = new Token();

header("Access-Control-Allow-Origin: *");
header("Authorization: Bearer ".$token->pegarTokenBearer());

//MÉTODO NÃO SUPORTADO
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "[ERRO 405] Método de requisição não suportado";
    http_response_code(405); 
    exit;
}

//TOKEN NÃO PASSADO OU ERRADO
if (is_null($token->verificarTokenBearer())) {
    echo "[ERRO 401] Não autorizado [1]";
    http_response_code(401); 
    exit;
}

//PEGA OS DADOS EM JSON E DECODIFICA PARA PHP
$request_raw = file_get_contents('php://input');
$request = json_decode($request_raw);

$loginDispositivo = new LoginDispositivo();

if (!isset($request->usuario) 
|| !isset($request->senha) 
|| $loginDispositivo->validar($request->usuario, $request->senha) === false) {
    echo "[ERRO 401] Não autorizado [2]";
    http_response_code(401); 
    exit;
}

$gps = new Gps();

$dispositivo = $request->dispositivo;
$info = $request->info;

if ($gps->cadastrar($dispositivo, $info)) {
    http_response_code(200); 
    exit;
} else {
    echo "[ERRO 400] Erro, impossivel cadastrar";
    http_response_code(400); 
    exit;
}