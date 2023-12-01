<?php
namespace models;

class Seguranca  
{    
    /**
     * usuarioLogado
     * VERIFICA SE USUÁRIO ESTÁ AUTENTICADO
     * @return bool
     */
    public function usuarioLogado(): bool
    {
        if (!isset($_SESSION['usuario']['id'])) {
            return false;
            exit;
        }
        return true;
    }
    
    /**
     * gerarCsrfToken
     * GERA A SEGURANÇA UTLIZADA NOS FORMULÁRIOS - CSRF TOKEN
     * @return void
     */
    public function gerarCsrfToken()
    {
        if(session_status() === PHP_SESSION_NONE){ 
            session_start();
        }

        if(!isset($_SESSION['seguranca']['csrf_token'])
        || empty($_SESSION['seguranca']['csrf_token'])) {
            $_SESSION['seguranca']['csrf_token'] = hash('sha256', openssl_random_pseudo_bytes(20));
            // unset($_SESSION['seguranca']['csrf_token']);
        }
    }
}