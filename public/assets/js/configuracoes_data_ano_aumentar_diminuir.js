document.addEventListener('DOMContentLoaded', () => {
    let tela_ano_viagem = document.querySelector('#tela_configuracoes #conteudo div[data-acao="viagens_data"]');
    let anos = tela_ano_viagem.querySelectorAll('div[data-ano]');
    let ano_exibir = null;

    if (dados_anos_viagens.length <= 1) {
        return;
    }

    //ANO ATIVO
    anos.forEach(ano => {
        let seta_aumentar = ano.querySelector('.seta_aumentar_ano');
        let seta_diminuir = ano.querySelector('.seta_diminuir_ano');

        if (ano.classList.contains('d-block')) {
            mostrarSetas(
                dados_anos_viagens.indexOf(parseInt(ano.textContent)),
                seta_aumentar,
                seta_diminuir
            );
        }

        seta_aumentar.addEventListener('click', (e) => {
            if (seta_aumentar.classList.contains('disabled')) {
                return;
            }

            let acao_seta = acaoSeta("aumentar", ano);

            if (acao_seta === false) {
                return;
            }

            mostrarSetas(
                dados_anos_viagens.indexOf(parseInt(ano.textContent)) - 1,
                seta_aumentar,
                seta_diminuir
            );

            ano.classList.remove('d-block');
            ano.classList.add('d-none');
        });

        seta_diminuir.addEventListener('click', (e) => {
            if (seta_diminuir.classList.contains('disabled')) {
                return;
            }

            let acao_seta = acaoSeta("diminuir", ano);

            if (acao_seta === false) {
                return;
            }

            mostrarSetas(
                dados_anos_viagens.indexOf(parseInt(ano.textContent)) + 1,
                seta_aumentar,
                seta_diminuir
            );

            ano.classList.remove('d-block');
            ano.classList.add('d-none');
        });
    });
    
    function acaoSeta(acao, ano)
    {
        let index_ano_viagem = dados_anos_viagens.indexOf(parseInt(ano.textContent));

        //AUMENTAR
        if (acao === 'aumentar') {
            if (typeof dados_anos_viagens[index_ano_viagem - 1] != "number") {
                return false;
            }
            
            //DECRESCER 1 PORQUE ARRAY ESTÁ VINDO DA DATA MAIS RECENTE PARA A MAIS ANTIGA
            ano_exibir = tela_ano_viagem.querySelector(`div[data-ano="${dados_anos_viagens[index_ano_viagem - 1]}"]`);
        }

        //DIMINUIR
        if (acao === 'diminuir') {
            if (typeof dados_anos_viagens[index_ano_viagem + 1] != "number") {
                return false;
            }

            //AUMENTAR 1 PORQUE ARRAY ESTÁ VINDO DA DATA MAIS RECENTE PARA A MAIS ANTIGA
            ano_exibir = tela_ano_viagem.querySelector(`div[data-ano="${dados_anos_viagens[index_ano_viagem + 1]}"]`);
        }

        if (ano_exibir === "undefined" || ano_exibir === null) {
            return false; 
        }

        ano_exibir.classList.remove('d-none');
        ano_exibir.classList.add('d-block');
    }

    function mostrarSetas(index_ano_viagem)
    {
        let seta_aumentar = tela_ano_viagem.querySelector(`div[data-ano="${dados_anos_viagens[index_ano_viagem]}"] .seta_aumentar_ano`);
        let seta_diminuir = tela_ano_viagem.querySelector(`div[data-ano="${dados_anos_viagens[index_ano_viagem]}"] .seta_diminuir_ano`);

        if (index_ano_viagem === 0) {
            seta_aumentar.classList.add('disabled');
            seta_diminuir.classList.remove('disabled');
            return;
        } else if (index_ano_viagem === dados_anos_viagens.length - 1) {
            seta_aumentar.classList.remove('disabled');
            seta_diminuir.classList.add('disabled');
            return;
        } else {
            seta_aumentar.classList.remove('disabled');
            seta_diminuir.classList.remove('disabled');
        }
    }
})