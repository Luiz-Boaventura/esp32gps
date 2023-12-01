
const configuracao_abrir = document.querySelector('.configuracoes .configuracao_abrir');
const configuracao_fechar = document.querySelector('#tela_configuracoes .configuracao_fechar');
const tela_configuracoes = document.querySelector('#tela_configuracoes');
const cabecalho_titulo = tela_configuracoes.querySelector('.cabecalho .titulo');
const box_links = tela_configuracoes.querySelector('#box_links');
const botoes_acao_tela_configuracoes = tela_configuracoes.querySelectorAll('a[data-acao]');
const tela_conteudo = tela_configuracoes.querySelector('#conteudo');
const icone_conteudo_voltar = tela_conteudo.querySelector('.conteudo_voltar');
const conteudos_area = tela_conteudo.querySelectorAll('div[data-acao]');

//ÍCONE ABRIR CONFIGURAÇÕES
configuracao_abrir.addEventListener('click', (e) => {
    //ABRIR TELA CONFIGURAÇÕES
    tela_configuracoes.classList.remove('d-none');
    tela_configuracoes.classList.add('d-block', 'transition_fade_in');
    setTimeout(() => {
        tela_configuracoes.classList.remove('transition_fade_in');
    }, 330); 

    //RETIRA O ÍCONE DE ENGRENAGEM
    configuracao_abrir.classList.remove('d-block');
    configuracao_abrir.classList.add('d-none');
});

//ÍCONE FECHAR CONFIGURAÇÕES
configuracao_fechar.addEventListener('click', (e) => {
    //FECHAR TELA CONFIGURAÇÕES
    tela_configuracoes.classList.remove('d-block');
    tela_configuracoes.classList.add('transition_fade_out');
    setTimeout(() => {
        tela_configuracoes.classList.add('d-none');
        tela_configuracoes.classList.remove('transition_fade_out');
    }, 330);

    //ADICIONA ÍCONE DE ENGRENAGEM
    configuracao_abrir.classList.remove('d-none');
    configuracao_abrir.classList.add('d-block');

    //FECHAR TELA CONTEÚDO
    tela_conteudo.classList.add('d-none');
    tela_conteudo.classList.remove('d-block');

    botoes_acao_tela_configuracoes.forEach(btnAcaoTelaConfiguracao => {
        //FECHAR JANELA AÇÃO
        btnAcaoTelaConfiguracao.classList.remove('d-none');
        btnAcaoTelaConfiguracao.classList.add('d-block');
        setTimeout(() => {
            btnAcaoTelaConfiguracao.classList.remove('d-block');
            btnAcaoTelaConfiguracao.classList.add('d-block');

            //ABRIR BOX LINKS
            box_links.classList.remove('d-none', 'd-md-none');
            box_links.classList.add('d-block', 'd-md-flex');
        }, 330);
    });

    //FECHAR CONTEÚDOS AREA
    conteudos_area.forEach(ca => {
        ca.classList.remove('d-block');
        ca.classList.add('d-none');
    });

    //ALTERA TÍTULO CABEÇALHO
    setTimeout(() => {
        cabecalho_titulo.textContent = "CONFIGURAÇÕES";
    }, 330);

    //RETIRA AÇÃO DO BOTÃO ICONE CONTEUDO VOLTAR
    setarAcaoIconeConteudoVoltar();
});

//ÍCONE VOLTAR CONTEÚDO
icone_conteudo_voltar.addEventListener('click', (e) => {
    //RETIRAR DATA ACAO DO ICONE CONTEUDO VOLTAR
    if (icone_conteudo_voltar.hasAttribute('data-acao')) {
        voltarTela(icone_conteudo_voltar.dataset.acao);
        icone_conteudo_voltar.removeAttribute('data-acao');
        return;
    }

    voltarTela();
});

//BOTÕES AÇÃO TELA CONFIGURAÇÕES
botoes_acao_tela_configuracoes.forEach(btn => {
    btn.addEventListener('click', (e) => {
        //ABRIR JANELA CONTEÚDO
        tela_conteudo.classList.remove('d-none');
        tela_conteudo.classList.add('d-block');

        //FECHAR BOX LINKS
        box_links.classList.remove('d-block');
        box_links.classList.add('d-none', 'd-md-none');

        let conteudo = tela_conteudo.querySelector(`[data-acao=${btn.dataset.acao}]`);

        cabecalho_titulo.textContent = `${conteudo.dataset.titulo}`;
        cabecalho_titulo.classList.add('transition_fade_in');
        setTimeout(() => {
            tela_configuracoes.classList.remove('transition_fade_in');
        }, 330); 

        //ABRIR JANELA AÇÃO
        conteudo.classList.remove('d-none');
        conteudo.classList.add('d-block', 'transition_fade_in');
        setTimeout(() => {
            conteudo.classList.remove('transition_fade_in');
        }, 330); 
    });
});

function abrirTela(tela = null)
{
    let tela_abrir = tela_conteudo.querySelector(`[data-acao="${tela}"]`)
    cabecalho_titulo.textContent = tela_abrir.dataset.titulo;

    cabecalho_titulo.classList.add('transition_fade_in');
    setTimeout(() => {
        cabecalho_titulo.classList.remove('transition_fade_in');
    }, 330); 

    conteudos_area.forEach(ca => {
        if (ca.dataset.acao == tela_abrir.dataset.acao) {
            ca.classList.remove('d-none');
            ca.classList.add('d-block', 'transition_fade_in');

            setTimeout(() => {
                ca.classList.remove('transition_fade_in');
            }, 330); 
        } else {
            ca.classList.remove('d-block');
            ca.classList.add('d-none');
        }
    });
}

function voltarTela(tela = null) 
{
    if (tela == null) {
        cabecalho_titulo.textContent = "CONFIGURAÇÕES";

        conteudos_area.forEach(ca => {
            ca.classList.remove('d-block');
            ca.classList.add('d-none');
        });

        tela_conteudo.classList.remove('d-block');
        tela_conteudo.classList.add('d-none');
        
        //ABRIR BOX LINKS
        box_links.classList.remove('d-none', 'd-md-none');
        box_links.classList.add('d-block', 'd-md-flex', 'transition_fade_in');
        setTimeout(() => {
            cabecalho_titulo.classList.remove('transition_fade_in');
        }, 330); 

        return;
    } 

    let tela_voltar = tela_conteudo.querySelector(`div[data-acao="${tela}"]`);

    cabecalho_titulo.textContent = tela_voltar.dataset.titulo;

    cabecalho_titulo.classList.add('transition_fade_in');
    setTimeout(() => {
        cabecalho_titulo.classList.remove('transition_fade_in');
    }, 330); 

    conteudos_area.forEach(ca => {
        if (ca.dataset.acao == tela_voltar.dataset.acao) {
            ca.classList.remove('d-none');
            ca.classList.add('d-block', 'transition_fade_in');

            setTimeout(() => {
                ca.classList.remove('transition_fade_in');
            }, 330); 
        } else {
            ca.classList.remove('d-block');
            ca.classList.add('d-none');
        }
    });
}

function setarAcaoIconeConteudoVoltar(tela = null) 
{
    if (tela != null) {
        icone_conteudo_voltar.dataset.acao = tela;
        return;
    }
    
    icone_conteudo_voltar.removeAttribute('data-acao');
}