<?php
    include_once(__DIR__.'/../models/Seguranca.php');
    include_once(__DIR__.'/../config/config.php');

    use \models\Seguranca;

    //TIMEZONE PARA CORRIGIR 1 HORA A MAIS DO HORÁRIO DE VERÃO DE SP
    date_default_timezone_set('America/Bahia');

    //INICIA  A SESSÃO
    if (!isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    //CSRF_TOKEN
    $seguranca = new Seguranca();
    $seguranca->gerarCsrfToken();

    $url = (isset($_GET['url']) && !empty($_GET['url'])) ? $_GET['url'] : 'home';
    $url = array_filter(explode('/',$url));
    
    $prefix = 'views/';
    
    //VIEW ACTION
    if(isset($url[1]) && $url[1] === 'action') {
        for ($x = 0; $x <= count($url) - 1; $x++) {
            if ($x === 0) {
                continue;
            }
    
            if ($x === 0) {
                $prefix .= $url[$x];
            } else {
                $prefix .= '/'.$url[$x];
            }
            
            $file = $prefix.'.php';
    
            if (file_exists($file)){
                include $file;
                exit;
            } 
    
            if ($x === count($url) - 1) {
                $file = $prefix.'404.php';
                include $file;
                exit;
            }
        }

        include $file;
        exit;
    }

    //API
    if (isset($url[0]) && $url[0] === 'api') {
        $prefix = "";

        for ($x = 0; $x <= count($url) - 1; $x++) {    
            if ($x === 0) {
                $prefix .= $url[$x];
            } else {
                $prefix .= '/'.$url[$x];
            }
            
            $file = $prefix.'.php';

            if (file_exists($file)){
                include $file;
                exit;
            } 
    
            if ($x === count($url) - 1) {
                $file = $prefix.'404.php';
                include $file;
                exit;
            }
        }

        include $file;
        exit;
    }

    $file = '';
    
    //PÁGINA INICIAL - HOME
    if (count($url) === 0 || (count($url) === 1 && $url === "home")) {
        $file = $prefix.'home.php';
        include $file;
        exit;
    }

    //PÁGINA É A PRÓRPIA URL (ÚLTIMO É O ARQUIVO)
    $url_implode = implode("/", $url);
    if (file_exists($prefix.$url_implode.".php")) {
        include $prefix.$url_implode.".php";
        exit;
    }

    //VERIFICA QUAL É A PAGINA
    for ($x = 0; $x <= count($url) - 1; $x++) {
        if ($x === 0) {
            $prefix .= $url[$x];
        } else {
            $prefix .= '/'.$url[$x];
        }

        $file = $prefix.'.php';

        if (file_exists($file)){
            include $file;
            exit;
        } 

        //PÁGINA NÃO ENCONTRADA - ERRO 404
        if ($x === count($url) - 1) {
            $file = 'views/404.php';
            include $file;
            exit;
        }
    }        
?>