document.addEventListener("DOMContentLoaded", () => {
    if (sessao_area_config === undefined || sessao_area_config === null) {
        return;
    }

    const tela_configuracoes = document.querySelector('#tela_configuracoes');
    const cabecalho_titulo = tela_configuracoes.querySelector('.cabecalho .titulo');
    const box_links = tela_configuracoes.querySelector('#box_links');
    const tela_conteudo = tela_configuracoes.querySelector('#conteudo');

    //ABRIR TELA CONFIGURAÇÕES
    tela_configuracoes.classList.remove('d-none');
    tela_configuracoes.classList.add('d-block', 'transition_fade_in');
    setTimeout(() => {
        tela_configuracoes.classList.remove('transition_fade_in');
    }, 330); 

    //FECHAR BOX LINKS
    box_links.classList.remove('d-block');
    box_links.classList.add('d-none', 'd-md-none');

    //ABRIR JANELA CONTEÚDO
    tela_conteudo.classList.remove('d-none');
    tela_conteudo.classList.add('d-block');

    let conteudo = tela_conteudo.querySelector(`[data-acao=${sessao_area_config}]`);

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