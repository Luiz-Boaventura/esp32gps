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
if ($request->ultimo_id_viagem_atual == "null") {
    $ultimo_id_viagem_atual = null;
} else {
    //ÚLTIMO ID VIAGEM ATUAL
    $ultimo_id_viagem_atual = intval($request->ultimo_id_viagem_atual);

    if(preg_match('/^[0-9]{1,11}$/', $ultimo_id_viagem_atual) != 1 || $ultimo_id_viagem_atual === 0) {
        echo json_encode([
            "status" => false,
            "mensagem" => "Erro! ID da última viagem é inválido"
        ]);
        exit;
    } 
}

$gps = new Gps();

$coordenadas_do_dia_atualizadas = $gps->pegarCoordenadasDoDiaAtualizadas(
    $_SESSION['dispositivo']['id'],
    $ultimo_id_viagem_atual
);

if (count($coordenadas_do_dia_atualizadas) > 0) {
    echo json_encode([
        "status" => true,
        "mensagem" => "Sucesso! Coordenadas do dia foram atualizadas",
        "coordenadas" => $coordenadas_do_dia_atualizadas
    ]);
    exit;
} else {
    echo json_encode([
        "status" => false,
        "mensagem" => "Erro! Não há coordenadas atuais para mostrar"
    ]);
}

