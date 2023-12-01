<?php
//RETORNA DADOS EM JSON PARA FETCH API - CHAMADA ASSÍNCRONA
include_once(__DIR__.'/../../../../models/Seguranca.php');
include_once(__DIR__.'/../../../../models/Gps.php');

use \models\Seguranca;
use \models\Gps;

$seguranca = new Seguranca();
if($seguranca->usuarioLogado() === false) {
    header('Location: '.BASE.'login');
    exit;
}

if (!isset($_SESSION['dispositivo']['id'])
|| is_null($_SESSION['dispositivo']['id'])
|| empty($_SESSION['dispositivo']['id'])) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Erro, selecione um dispositivo para continuar"
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode([
        "status" => false,
        "mensagem" => "Erro inesperado do servidor (1), tente novamente"
    ]);
    exit;
}

$request_raw = file_get_contents('php://input');
$request = json_decode($request_raw);

$csrf_token = $request->csrf_token;
$data_br = $request->data_br;

//NÃO EXISTE OU É VAZIO
if (!isset($csrf_token)
|| empty($csrf_token)
|| $csrf_token != $_SESSION['seguranca']['csrf_token']) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Erro inesperado do servidor (2), tente novamente"
    ]);
    exit;
}

if (empty($data_br)) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Data está vazia"
    ]);
    exit;
}

//VALIDAÇÕES
if(strlen($data_br) != 10) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Data incorreta (faltando ou excedendo a quantidade de caracteres)"
    ]);
    exit;
}
if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $data_br) != 1) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Data não tem um formato válido"
    ]);
    exit;
} 

//VERIFICAR SE A DATA É VALIDA
$data_br_datetime = DateTime::createFromFormat('d/m/Y', $data_br);
$data_erro = DateTime::getLastErrors();

if ($data_erro['warning_count'] !== 0 || $data_erro['error_count'] !== 0) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Data inválida"
    ]);
    exit;
} 

$arr = [];

$gps = new Gps();
$mostrar_viagens = $gps->mostrarViagens($_SESSION['dispositivo']['id'], $data_br);

if (count($mostrar_viagens) > 0) {
    $arr["status"] = true;
    $arr["mensagem"] = "Foram encontradas ".count($mostrar_viagens). " viagens";
    $arr["viagens"] = $mostrar_viagens;

    echo json_encode($arr);
    exit;
} else {
    $arr["status"] = false;
    $arr["mensagem"] = "Não foi encontrada nenhuma viagem para a data selecionada";
    $arr["retorno"] = $mostrar_viagens;

    echo json_encode($arr);
    exit;
}