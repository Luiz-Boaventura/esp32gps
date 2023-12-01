document.addEventListener('DOMContentLoaded', () => {
    const configuracao_fechar = document.querySelector('#tela_configuracoes .configuracao_fechar');
    const endpoint = BASE + "action/json/retornar-viagens-pela-data";
    let tela_ano_viagem = document.querySelector('#tela_configuracoes #conteudo div[data-acao="viagens_data"]');
    let calendario_dias = tela_ano_viagem.querySelectorAll('.dia[data-databr]');
    let tela_inicial_configuracoes_viagem = document.querySelector('.configuracoes .dispositivo_viagem .viagem');
        
    calendario_dias.forEach(dia => {
        dia.addEventListener('click', () => {
            let data_br = dia.dataset.databr;

            if (data_br.length <= 0) {
                return;
            }

            const carregando = document.getElementById('carregando');
            carregando.style.display = 'flex';

            // Defina um tempo limite em milissegundos (por exemplo, 10 segundos)
            const tempo_gif = 400;
            let gifTimer;
            
            gifTimer = setTimeout(() => {
                // O código dentro deste bloco será executado após o tempo mínimo do GIF
                // Cancelar o timer para garantir que ele não seja executado duas vezes
                clearTimeout(gifTimer);

                const fetchPromise = fetch(endpoint, {
                    method: "POST",
                    mode: "same-origin",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        "data_br": data_br,
                        "csrf_token": csrf_token
                    })
                })
                .then(response => response.json())//response.json())  // converter para json
                .then(response => exibirViagens(response, data_br))
                .then(json => console.log(json))    //imprimir dados no console
                .catch(err => console.log('Erro de solicitação', err)) // lidar com os erros por catch
                .finally(() => {
                    carregando.style.display = 'none';
                });

                // Cancelar o timer se a requisição for concluída antes do tempo mínimo do GIF
                fetchPromise.then(() => {
                    clearTimeout(gifTimer);
                });
            }, tempo_gif);
        });
    });

    function exibirViagens(response, data_br) 
    {
        setarAcaoIconeConteudoVoltar("viagens_data");
        abrirTela("viagens_numero");
        
        if (!response.status) {
            voltarTela("viagens_data");
            return;
        }

        let viagens = response.viagens;

        let conteudo_viagem_numero = tela_configuracoes.querySelector('div[data-acao="viagens_numero"]');
        conteudo_viagem_numero.innerHTML = '';

        let html_criar_viagens = '';

        html_criar_viagens +=   `<div class="titulo_conteudo">Selecione uma viagem de ${data_br}</div>
                                    <div class="d-block">
                                        <div class="d-flex flex-wrap">`;
        
        for (let x = 0; x <= viagens.length - 1; x++) {
            html_criar_viagens += `         <div class="col-6 col-md-3 col-lg-3 col-xl-2 d-flex mt-3">
                                                <div class="selecionar_viagem" data-viagem="${x}">
                                                    <div class="titulo_viagem">Viagem</div>
                                                    <div class="dia_viagem">${x + 1}</div>
                                                </div>
                                            </div>`;
        }

        html_criar_viagens +=   `       </div>
                                    </div>
                                </div>`;

        conteudo_viagem_numero.innerHTML = html_criar_viagens;

        let html_viagens = conteudo_viagem_numero.getElementsByClassName('selecionar_viagem');

        for (let x = 0; x <= html_viagens.length - 1; x++) {
            html_viagens[x].addEventListener('click', () => {
                let viagem = html_viagens[x];
                let numero_viagem = viagem.dataset.viagem;

                if (viagens[numero_viagem] === "undefined") {
                    //ERRO VIAGEM NÃO ENCONTRADA;
                    return;
                }

                tela_inicial_configuracoes_viagem.textContent = `Viagem: ${data_br} - ${x + 1}`;
                tela_inicial_configuracoes_viagem.classList.remove('d-none');

                // let montarMapa = remontarMapa(viagens[numero_viagem], parseInt(numero_viagem));
                let montarMapa = remontarMapa(viagens[numero_viagem], false);

                //atualizar mapa do arquivo app.js
                atualizar_mapa = false;

                if (montarMapa === true) {
                    configuracao_fechar.click();
                }
            });
        }
    }
});