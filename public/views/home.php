<?php 
include_once('seguranca.php'); 

include_once(__DIR__.'/../../models/Dispositivo.php');
include_once(__DIR__.'/../../models/Gps.php');

use \models\Dispositivo;
use \models\Gps;

$dispositivo_selecionado = (isset($_SESSION['dispositivo']['id']))? $_SESSION['dispositivo']['id'] : 2;

$gps = new Gps();
$dispositivo = new Dispositivo();

//PEGAR COORDENADAS DO DIA
$dados_gps = $gps->pegarTodasCoordenadasDoDia($dispositivo_selecionado);

//PEGAR DATAS DE VIAGENS DO ANO ATUAL
$datas_viagens_ano = $gps->mostrarDatasViagensDispositivo($dispositivo_selecionado, date("Y"));

//PEGAR DISPOSITIVOS
$dados_dispositivos = $dispositivo->pegarTodos();

//ABERTURA TELA CONFIG
$sessao_area_config = json_encode(null);
if (isset($_SESSION['area_config'])) {
    $sessao_area_config = json_encode($_SESSION['area_config']);
}

//MENSAGEM ERRO OU SUCESSO
$sessao_mensagem = json_encode(null);
if (isset($_SESSION['mensagem']['erro']) || isset($_SESSION['mensagem']['sucesso'])) {
    $sessao_mensagem = json_encode($_SESSION['mensagem']);
}

//VARIÁVEIS JS
$dados_gps_js = json_encode($dados_gps);
$dados_gps_quantidade_js = json_encode(count($dados_gps));
$dados_dispositivos_js = json_encode($dados_dispositivos);

//PEGAR ANOS VIAGENS
$arr_anos_viagens = [];
foreach($datas_viagens_ano as $key_ano => $dva) {
    if (!in_array(intval($key_ano), $arr_anos_viagens)) {
        array_push($arr_anos_viagens, $key_ano);
    }
}

$dados_anos_viagens_js = json_encode($arr_anos_viagens);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESP32 GPS mapa</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/bootstrap/bootstrap.min.css<?= HTML_VERSION; ?>">
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/bootstrap/bootstrap-icons.css<?= HTML_VERSION; ?>">

    <!-- LEAFLET -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/leaflet.css">

    <!-- ESP32GPS -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/style.css<?= HTML_VERSION; ?>">
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/toast.css<?= HTML_VERSION; ?>">
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/gif_load.css<?= HTML_VERSION; ?>">

    <!-- VALIDAÇÃO -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/validacao.css<?= HTML_VERSION; ?>">
</head>
<body>
    <!-- CONFIGURAÇÕES -->
    <?php include_once 'partials/configuracoes.php'; ?>

    <!-- TOAST MENSAGEM -->
    <?php if(isset($_SESSION['mensagem']['erro']) || isset($_SESSION['mensagem']['sucesso'])): ?>
        <?php 
            $status = (isset($_SESSION['mensagem']['sucesso']))? 'sucesso' : 'erro'; 
            $mensagem = (isset($_SESSION['mensagem']['sucesso']))? $_SESSION['mensagem']['sucesso'] : $_SESSION['mensagem']['erro']; 
        ?>

        <div class="toast <?= $status; ?> align-items-center text-white border-0 position-fixed p-1 me-2 mb-4 bottom-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999">
            <div class="d-flex">
                <div class="toast-body"><?= $mensagem; ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>   

        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <!-- FETCH API - CARREGANDO -->
    <div id="carregando">
        <div id="loading-bar-spinner" class="spinner"><div class="spinner-icon"></div></div>
    </div>

    <!-- MAPA -->
    <div id="map"></div>

    <!-- JS -->
    <!-- LEAFTLET -->
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/leaflet/leaflet.js<?= HTML_VERSION; ?>"></script>

    <script>
        const BASE = "<?= BASE; ?>";
        var sessao_area_config = <?= $sessao_area_config; ?>;
        var dados_gps = <?= $dados_gps_js ?>;
        var dados_gps_quantidade = <?= $dados_gps_quantidade_js; ?>;
        var dados_dispositivos = <?= $dados_dispositivos_js; ?>;
        var dados_anos_viagens = <?= $dados_anos_viagens_js; ?>;
        var csrf_token = <?= json_encode($_SESSION['seguranca']['csrf_token']); ?>;
    </script>

    <!-- BOOTSTRAP -->
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/bootstrap/popper.min.js<?= HTML_VERSION; ?>"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/bootstrap/bootstrap.min.js<?= HTML_VERSION; ?>"></script>

    <!-- BOOTSTRAP TOAST -->
    <script>
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, {
                animation: true,
                autohide: true,
                delay: 2500
            });
        })
    </script>

    <script>
        var sessao_mensagem = <?= $sessao_mensagem; ?>

        if (sessao_mensagem != null) {
            let toast = document.querySelector('.toast');
            let myToast = bootstrap.Toast.getOrCreateInstance(toast);
            myToast.show();
        }
    </script>

    <!-- CUSTOM 3 -->
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/validacoes.js"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/configuracoes.js"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/configuracoes_abrir_telas.js"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/configuracoes_data_ano_aumentar_diminuir.js"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/configuracoes_data_selecionar.js"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/app.js"></script>
</body>
</html>