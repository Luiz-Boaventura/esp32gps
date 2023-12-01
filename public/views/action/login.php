<?php
include_once(__DIR__.'/../../../models/Usuario.php');

use \models\Usuario;

$link_erro = BASE."login";
$_SESSION['login_mensagem'] = true;

//CSRF TOKEN
if(!isset($_SESSION['seguranca']['csrf_token'])
|| !isset($_POST['csrf_token'])
|| $_SESSION['seguranca']['csrf_token'] != $_POST['csrf_token']) {
    $_SESSION['mensagem']['erro'] = 'Erro inesperado do servidor, tente novamente';
    header('Location: '.$link_erro);
    exit;
}

//SE NÃO EXISTIR NOME USUÁRIO OU FOR VAZIO
if(!isset($_POST['usuario']) || empty($_POST['usuario'])) {
    $_SESSION['mensagem']['erro'] = 'Preencha o campo Nome Completo';
    header('Location: '.$link_erro);
    exit;
}

//SE NÃO EXISTIR SENHA OU FOR VAZIA
if(!isset($_POST['senha']) || empty($_POST['senha'])) {
    $_SESSION['mensagem']['erro'] = 'Preencha o campo Senha';
    header('Location: '.$link_erro);
    exit;
}

//POSTS RECEBIDOS
$usuario_nome = addslashes(trim($_POST['usuario']));
$usuario_senha = addslashes($_POST['senha']);

$usuario = new Usuario();

if($usuario->login($usuario_nome, $usuario_senha)) {
    unset($_SESSION['login_mensagem']);
    
    header('Location: '.BASE);
    exit;
} else {
    $_SESSION['post_usuario'] = $_POST['usuario'];
    $_SESSION['mensagem']['erro'] = 'Erro! Credenciais incorretas.';
    header('Location: '.$link_erro);
    exit;
}