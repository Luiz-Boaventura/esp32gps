<?php 
    if (isset($_SESSION['usuario']['id'])) {
        header("Location: ".BASE);
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESP32GPS - LOGIN</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/bootstrap/bootstrap.min.css<?= HTML_VERSION; ?>">
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/bootstrap/bootstrap-icons.css<?= HTML_VERSION; ?>">

    <!-- CUSTOM -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_ASSETS; ?>css/style.css<?= HTML_VERSION; ?>">
</head>
<body>
    <!-- CMS LOGIN -->
    <section class="container-fluid" id="login">
        <div class="container h-100 d-flex justify-content-center align-items-center">
            <div class="col-12 d-block">

                <!-- MENSAGEM ERRO -->
                <div class="d-flex justify-content-center">
                    <div class="col-12 col-md-8 col-lg-5">
                        <?php include_once __DIR__."/../../public/views/partials/formulario_mensagens/login.php"; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="col-12 col-md-8 col-lg-5 my-3">
                        <div class="titulo_login d-block">ESP32 GPS</div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <form class="form_custom col-12 col-md-8 col-lg-5 d-block row g-3" action="<?= BASE; ?>action/login" method="POST">
                        <!-- EMAIL -->
                        <div class="col-12 d-block">
                            <label class="h5" for="usuario">Usuário:</label>
                            <input name="usuario" id="usuario" type="text" class="form-control" value="<?= (isset($_SESSION['post_usuario'])?$_SESSION['post_usuario']:'')?>" autocomplete="on" required />
                        </div>

                        <!-- SENHA -->
                        <div class="col-12 ">
                            <label class="h5" for="senha">Senha:</label>
                            <input name="senha" id="senha" type="password" class="form-control" required />
                        </div>

                        <!-- CSRF TOKEN -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['seguranca']['csrf_token']; ?>">

                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <button type="submit" class="submit_btn btn btn-primary login"><i class="bi bi-person-circle me-2"></i>Login</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- BOOTSTRAP -->
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/bootstrap/popper.min.js<?= HTML_VERSION; ?>"></script>
    <script type="text/javascript" src="<?= BASE_ASSETS; ?>js/bootstrap/bootstrap.min.js<?= HTML_VERSION; ?>"></script>
</body>
</html>