<?php
include_once('seguranca.php'); 
include_once(__DIR__.'/../../../models/Usuario.php');

use \models\Usuario;

$_SESSION['area_config'] = 'alterar_dados';
$_SESSION['configuracoes_alterar_dados_mensagem'] = true;

//CSRF TOKEN
if(!isset($_SESSION['seguranca']['csrf_token'])
|| !isset($_POST['csrf_token'])
|| $_SESSION['seguranca']['csrf_token'] != $_POST['csrf_token']) {
    $_SESSION['mensagem']['erro'] = 'Erro inesperado do servidor, tente novamente';
    header('Location: '.BASE);
    exit;
}

//SE NÃO EXISTIR NOME OU FOR VAZIO
if(!isset($_POST['nome_usuario']) || empty($_POST['nome_usuario'])) {
    $_SESSION['mensagem']['erro'] = 'Preencha o campo Nome de Usuário';
    header('Location: '.BASE);
    exit;
}

//SE NÃO EXISTIR NOME OU FOR VAZIO
if(!isset($_POST['senha']) || empty($_POST['senha'])) {
    $_SESSION['mensagem']['erro'] = 'Preencha o campo Senha';
    header('Location: '.BASE);
    exit;
}

if(!isset($_POST['senha_confirmar']) || empty($_POST['senha_confirmar'])) {
    $_SESSION['mensagem']['erro'] = 'Preencha o campo Confirmar Senha';
    header('Location: '.BASE);
    exit;
}

//POSTS RECEBIDOS
$nome_usuario = $_POST['nome_usuario'];
$senha = $_POST['senha'];
$senha_confirmar = $_POST['senha_confirmar'];

//NOME
if(strlen($nome_usuario) < 1 || strlen($nome_usuario) > 120) {
    $_SESSION['mensagem']['erro'] = 'Campo Nome de Usuário deve ter mínimo 1 caractere e no máximo 120 caracteres';
    header('Location: '.BASE);
    exit;
}
if(preg_match('/^[0-9A-Za-zÀ-ÖØ-öø-ÿ\s]{1,120}$/', $nome_usuario) != 1) {
    $_SESSION['mensagem']['erro'] = 'Campo Nome de Usuário contém algum caractere inválido';
    header('Location: '.BASE);
    exit;
} 

//SENHA
if(strlen($senha) < 4 || strlen($senha) > 50) {
    $_SESSION['mensagem']['erro'] = 'Campo Senha deve ter mínimo 4 caracteres e no máximo 50 caracteres';
    header('Location: '.BASE);
    exit;
}
if(preg_match('/^[0-9A-Za-zÀ-ÖØ-öø-ÿ`!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?~]{4,50}$/', $senha) != 1) {
    $_SESSION['mensagem']['erro'] = 'Campo Senha contém algum caractere inválido';
    header('Location: '.BASE);
    exit;
} 

//CONFIRMAR SENHA
if(strlen($senha_confirmar) < 4 || strlen($senha_confirmar) > 50) {
    $_SESSION['mensagem']['erro'] = 'Campo Confirmar Senha deve ter mínimo 4 caracteres e no máximo 50 caracteres';
    header('Location: '.BASE);
    exit;
}
if(preg_match('/^[0-9A-Za-zÀ-ÖØ-öø-ÿ`!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?~]{4,50}$/', $senha_confirmar) != 1) {
    $_SESSION['mensagem']['erro'] = 'Campo Confirmar Senha contém algum caractere inválido';
    header('Location: '.BASE);
    exit;
} 

//VERIFICA SE AS SENHAS SÃO IGUAIS
if($senha != $senha_confirmar) {
    return false;
    exit;
}

//VARIÁVEIS A SEREM INSERIDAS
$nome_usuario = addslashes(preg_replace('/\s+/', ' ', trim($nome_usuario)));
$senha = addslashes($senha);
$senha_confirmar = addslashes($senha_confirmar);

$usuario = new Usuario();

//VERIFICAR SE NOME DE USUÁRIO JÁ EXISTE
if($usuario->existe($nome_usuario)) {
    $_SESSION['configuracoes_alterar_dados_mensagem'] = true;
    $_SESSION['mensagem']['erro'] = 'Erro! Nome de Usuário indisponível';
    header('Location: '.BASE);
    exit;
}

if($usuario->atualizar(intval($_SESSION['usuario']['id']), $nome_usuario, $senha)) {
    unset($_SESSION['configuracoes_alterar_dados_mensagem']);

    $_SESSION['login_mensagem'] = true;
    $_SESSION['mensagem']['sucesso'] = 'Sucesso! Você alterou o perfil, faça o login novamente.';
    
    header('Location: '.BASE.'login');
    exit;
} else {
    $_SESSION['mensagem']['erro'] = 'Erro! Não foi possível alterar o perfil.';
    header('Location: '.BASE);
    exit;
}