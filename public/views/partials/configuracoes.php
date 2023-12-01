<div class="configuracoes">
    <div class="configuracao_abrir d-block">
        <i class="bi bi-gear"></i>
    </div>

    <div class="dispositivo_viagem d-block d-md-flex">
        <div class="dispositivo">Dispositivo: <?= $_SESSION['dispositivo']['nome']; ?></div>
        <div class="viagem mt-1 mt-md-0 ms-md-2 d-none"></div>
    </div>
</div>

<div id="tela_configuracoes" class="d-none">
    <div id="areas" class="d-block">
        <div class="container-fluid px-0 mx-0 cabecalho">
            <div class="col-12 d-block titulo">CONFIGURAÇÕES</div>

            <a class="configuracao_fechar d-block">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>

        <div id="box_links" class="container d-block d-md-flex py-5">
            <div class="col-12">
                <div class="col-12 d-none justify-content-center">
                    <a class="ver_em_tempo_real d-flex justify-content-center align-items-center col-12 col-md-5 text-center " onclick="montarMapa();">
                        <div class="icone_tempo_real"></div>
                        <span>VER EM TEMPO REAL</span>
                    </a>
                </div>

                <div class="col-12 d-block d-md-flex flex-wrap justify-content-between justify-content-lg-around">
                    <a class="col-12 col-md-5 text-center me-md-2" data-acao="alterar_dados">
                        <i class="bi bi-person-circle"></i>
                        <span>ALTERAR DADOS</span>
                    </a>

                    <a class="col-12 col-md-5 text-center ms-md-2" data-acao="dispositivos">
                        <i class="bi bi-pci-card"></i>
                        <span>DISPOSITIVOS</span>
                    </a>
                
                    <a class="col-12 col-md-5 text-center me-md-2" data-acao="viagens_data">
                        <i class="bi bi-geo-alt"></i>
                        <span>VIAGENS</span>
                    </a>

                    <a class="col-12 col-md-5 text-center ms-md-2" href="<?= BASE."action/sair"; ?>">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>SAIR</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="conteudo" class="d-none">
        <div class="container">
            <div class="col-12">
                <a class="col-2 d-block conteudo_voltar">
                    <i class="bi bi-arrow-left"></i>
                </a>

                <div data-acao="alterar_dados" data-titulo="ALTERAR DADOS" class="d-none">
                    <div class="corpo_conteudo">
                        <?php include_once "formulario_mensagens/configuracoes_alterar_dados.php"; ?>

                        <form class="configuracoes_alterar_dados form_custom borda_formulario px-3 py-3" action="<?= BASE."action/alterar-perfil";?>" method="POST">
                            <div class="row g-3 mb-4">
                                <!-- USUÁRIO -->
                                <div class="col-12 col-md-6 mt-4">
                                    <label>* NOME DE USUÁRIO:</label>
                                    <input name="nome_usuario" type="text" value="<?= $_SESSION['usuario']['nome']; ?>" class="form-control" data-validacao="nome_usuario" required />
                                </div>
                
                                <!-- SENHA -->
                                <div class="col-12 col-md-6 mt-4">
                                    <label>* SENHA:</label>
                                    <input name="senha" type="password" class="form-control" data-validacao="senha"  data-validacao-campo-confirmar="senha_confirmar" required />
                                </div>
                            </div>

                            <div class="row g-3 mb-4 d-flex justify-content-center">
                                <!-- CONFIRMAR SENHA -->
                                <div class="col-12 col-md-6 mt-4">
                                    <label>* CONFIRMAR SENHA:</label>
                                    <input name="senha_confirmar" type="password" class="form-control" data-validacao="confirmar_senha" data-validacao-campo-confirmar="senha" required />
                                </div>
                            </div>

                            <!-- CSRF TOKEN -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['seguranca']['csrf_token']; ?>">

                            <div class="row g-3 mb-4 d-flex justify-content-center">
                                <a type="submit"><i class="bi bi-pencil"></i> ALTERAR DADOS</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div data-acao="dispositivos" data-titulo="DISPOSITIVOS" class="d-none">
                    <div class="titulo_conteudo">Selecione um dispositivo para rastrear</div>

                    <div class="d-flex flex-wrap">
                        <!-- DISPOSITIVO ATIVO -->
                         <div class="col-12 col-md-4 col-lg-3 mx-0 mx-md-2">
                            <a class="ativo">
                                <div class="caixa_dispositivo_id">
                                    <span class="d-flex align-items-center ativo"><?= "ID #{$_SESSION['dispositivo']['id']}"; ?></span>
                                </div>
                                <div class="caixa_dispositivo_info">
                                    <?= $_SESSION['dispositivo']['nome']; ?>
                                </div>
                            </a>
                        </div>

                        <!-- OUTROS DISPOSITIVOS -->
                        <?php foreach($dados_dispositivos as $dispositivo): ?>
                            <?php if(intval($dispositivo['id']) != $_SESSION['dispositivo']['id']): ?>
                                <div class="col-12 col-md-4 col-lg-3 mx-0 mx-md-2">
                                    <a href="<?= BASE.'action/dispositivo/trocar/'.$dispositivo['id']; ?>">
                                        <div class="caixa_dispositivo_id">
                                            <span class="d-flex align-items-center"><?= "ID #{$dispositivo['id']}"; ?></span>
                                        </div>
                                        <div class="caixa_dispositivo_info">
                                            <?= $dispositivo['nome']; ?>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div data-acao="viagens_data" data-titulo="VIAGEM" class="d-none">
                    <?php if(count($arr_anos_viagens) > 0): ?>
                        <div class="titulo_conteudo">Selecione uma data</div>
                    <?php endif; ?>

                    <?php 
                        $arr_meses = [
                            "01" => "janeiro",
                            "02" => "fevereiro",
                            "03" => "março",
                            "04" => "abril",
                            "05" => "maio",
                            "06" => "junho",
                            "07" => "julho",
                            "08" => "agosto",
                            "09" => "setembro",
                            "10" => "outubro",
                            "11" => "novembro",
                            "12" => "dezembro"
                        ];


                        if (count($datas_viagens_ano) > 0) {
                            $html_datas_viagens = '';
                            $arr_meses_iterados = [];

                            if (isset($_SESSION['viagem']['data'])) {
                                $data_selecionada = $_SESSION['viagem']['data'];
                                $data_selecionada_explode = explode("/", $data_selecionada);
                            } else {
                                $data_selecionada = reset($datas_viagens_ano)[0]['data_formatada_local'];
                                $data_selecionada_explode = explode("/", $data_selecionada);
                            }

                            foreach ($datas_viagens_ano as $key_ano => $dva) {
                                $mostrar_ano = 'd-none';
                                if (intval($key_ano) === intval($data_selecionada_explode[2])) {
                                    $mostrar_ano = 'd-block';
                                } 
                                
                                //MOSTRAR ANO
                                $html_datas_viagens .= '<div class="'.$mostrar_ano.'" data-ano="'.$key_ano.'">
                                                            <div class="col-12 d-flex align-items-center justify-content-around">
                                                                <div class="ano col-12 d-flex align-items-center justify-content-around">
                                                                    <a class="col-3 seta_diminuir_ano disabled d-flex justify-content-center align-items-center"><i class="bi bi-chevron-left"></i></a>
                                                                    <span class="col-6">'.$key_ano.'</span>
                                                                    <a class="col-3 seta_aumentar_ano disabled d-flex justify-content-center align-items-center"><i class="bi bi-chevron-right"></i></a>
                                                                </div>
                                                            </div>';

                                foreach ($dva as $key_datas => $datas) {
                                    $data_explode = explode("/", $datas['data_formatada_local']);
                                    $dia = intval($data_explode[0]);
                                    $mes = intval($data_explode[1]);
                                    $ano = intval($key_ano);

                                    if (intval($key_datas) === 0) {
                                        $html_datas_viagens .= '<div class="d-flex flex-wrap">';
                                    }

                                    //ABRIR OU FECHAR MÊS
                                    if (!in_array($mes, $arr_meses_iterados)) {
                                        array_push($arr_meses_iterados, $mes);

                                        $html_datas_viagens .= '<div class="col-12 mes my-3">'.$arr_meses[$data_explode[1]].'</div>';

                                    }

                                    //DATAS DO MÊS
                                    if ($data_selecionada === $datas['data_bd']) {
                                        $html_datas_viagens .= '<div class="col-6 col-md-3 col-lg-3 col-xl-2 d-flex mt-3">
                                                                    <div class="dia" data-databr="'.$datas['data_formatada_local'].'">
                                                                        <div class="conteudo_mes ativo">'.mb_substr($arr_meses[$data_explode[1]], 0, 3).'.</div>
                                                                        <div class="conteudo_dia ativo">'.$dia.'</div>
                                                                    </div>
                                                                </div>';
                                        continue;
                                    }

                                    $html_datas_viagens .= '<div class="col-6 col-md-3 col-lg-3 col-xl-2 d-flex mt-3">
                                                                <div class="dia" data-databr="'.$datas['data_formatada_local'].'">
                                                                    <div class="conteudo_mes">'.mb_substr($arr_meses[$data_explode[1]], 0, 3).'.</div>
                                                                    <div class="conteudo_dia">'.$dia.'</div>
                                                                </div>
                                                            </div>'; 

                                    if (intval($key_datas) === count($datas_viagens_ano[$key_ano]) - 1) {
                                        $arr_meses_iterados = [];

                                        $html_datas_viagens .= '</div>';
                                    }
                                }

                                //DIV FECHAR ANO
                                $html_datas_viagens .= '</div>';
                            }

                            //EXIBIR
                            echo $html_datas_viagens;
                        } else {
                            echo '<div>Não há datas para mostrar</div>';
                        }
                    ?>
                </div>
                <div data-acao="viagens_numero" data-titulo="VIAGEM"></div>
            </div>
        </div>
    </div>
</div>