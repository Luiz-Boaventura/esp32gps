<?php 
if(isset($_SESSION['login_mensagem']) && isset($_SESSION['login_mensagem'])) {
    //MENSAGEM ERRO
    if(isset($_SESSION['mensagem']['erro'])) {
        echo 
        '<div class="alert alert-danger d-flex align-items-center mt-4" role="alert">
            <span class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" role="img"></span>
            <div class="roboto-regular">
                '.$_SESSION['mensagem']['erro'].'
            </div>
        </div>';
    }

    //MENSAGEM SUCESSO
    if(isset($_SESSION['mensagem']['sucesso'])) {
        echo 
        '<div class="alert alert-success d-flex align-items-center mt-4" role="alert">
            <span class="bi bi-check-circle-fill flex-shrink-0 me-2" role="img"></span>
            <div class="roboto-regular">
                '.$_SESSION['mensagem']['sucesso'].'
            </div>
        </div>';
    }

    unset(
        $_SESSION['login_mensagem'],
        $_SESSION['mensagem']
    );
}

