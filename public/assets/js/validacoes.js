const regex = {
    nome_usuario: {
        min: 1,
        max: 120,
        pattern: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ\s]+$/
    },
    senha: {
        min: 4,
        max: 50,
        pattern: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ\`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]+$/
    },
    confirmar_senha: {
        min: 4,
        max: 50,
        pattern: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ\`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]+$/
    }
};
    
document.addEventListener('DOMContentLoaded', () => {
    var msgErros = [];

    ouvidorFormularios();

    function ouvidorFormularios()
    {
        let formularios = document.querySelectorAll('form');
        
        formularios.forEach(formulario => {
            let campos = formulario.querySelectorAll('input, select, checkbox, file, image, date, hour');
            let botao_enviar = formulario.querySelector('a[type="submit"]');

            //BOTÃO ENVIAR CHECK
            botao_enviar.addEventListener('click', (e) => {
                let validar = validarCampos(campos);

                console.log(validar);

                if (validar['tem_erro'] === false) {
                    limparMensagensErro(campos);

                    if (!formulario.classList.contains('js_fetch_assincrono')) {
                        formulario.submit();
                    }
                    return;
                } else {
                    e.preventDefault();   
                    exibirMensagensErro(campos, validar['msgErros']);
                }
            });

            //EVENTO VERIFICAR
            campos.forEach(campo => {
                ["keyup", "change", "focusout"].forEach(function (event) {
                    campo.addEventListener(event, (e) => {
                        let validar = validarCampos(campos);

                        if (validar['tem_erro'] === false) {
                            limparMensagensErro(campos);
                            return;
                        } else {
                            e.preventDefault();
                            exibirMensagensErro(campos, validar['msgErros']);
                        }
                    });
                });
            });
        });
    }

    function exibirMensagensErro(campos, msgErros) 
    {
        limparMensagensErro(campos);

        campos.forEach(campo => {
            if (msgErros[campo.name].length === 0) {
                return;
            }

            let el = document.createElement("div");
            let input_mensagens = '<ul>';

            el.classList.add(
                'box_validar_erros', 
                `validar_erro_${campo.name}`
            );

            msgErros[campo.name].forEach(msg_erro => {
                input_mensagens += `<li>${msg_erro}</li>`;
            });

            input_mensagens += '</ul>';

            el.innerHTML = input_mensagens;

            campo.closest('div').insertBefore(el, campo.nextSibling);
        });
    }

    function limparMensagensErro(campos)
    {
        campos.forEach(campo => {
            let erros = document.querySelectorAll(`.validar_erro_${campo.name}`);

            //REMOVER ERROS
            erros.forEach(err => {
                err.remove();
            });
        });
    }

    function validarCampos(campos) 
    {   
        let tem_erro = false;

        // Função para adicionar um erro ao objeto msgErros
        function adicionarErro(campo, mensagem) 
        {
            msgErros[campo.name].push(mensagem);
            tem_erro = true;
        }

        function validarPorObjRegex(campo, campo_validacao) 
        {
            if (!regex.hasOwnProperty(`${campo_validacao}`)) {
                return;
            }

            // let tipos_validar = regex[campo_dataset];
            let tipos_validar = regex[campo_validacao];

            for (let chave in tipos_validar) {
                if (tipos_validar.hasOwnProperty(chave)) {
                    switch (chave) {
                        case 'min':
                            if (campo.value.length < regex[campo_validacao]['min']) {
                                adicionarErro(campo, `Deve conter no mínimo ${regex[campo_validacao]['min']} caractere(s)`);  
                            }
                        break;
                        case 'max':
                            if (campo.value.length > regex[campo_validacao]['max']) {
                                adicionarErro(campo, `Deve conter no máximo ${regex[campo_validacao]['max']} caracteres`);  
                            }
                        break;
                        case 'pattern':
                            let regexp = new RegExp(regex[campo_validacao]["pattern"]);
                            
                            if (campo.value.length > 0 && campo.value.match(regexp) === null) {
                                adicionarErro(campo, 'Contém caractere(s) inválido(s)');
                            }
                        break; 
                    }
                }
            }

            return;
        }

        function validarConfirmacaoCampos(campo) 
        {
            if (campo.dataset.validacaoCampoConfirmar != undefined) {
                let form = campo.closest('form');

                let label_campo = form.querySelector(`*[name="${campo.name}"]`)
                                    .closest('div')
                                    .querySelector('label')
                                    .textContent
                                    .replace(/^\s*\*\s*|\s*:/g, '');
                
                let campo_confirmar = form.querySelector(`*[name="${campo.dataset.validacaoCampoConfirmar}"]`);
                let label_campo_confirmar = form.querySelector(`*[name="${campo.dataset.validacaoCampoConfirmar}"]`)
                                            .closest('div')
                                            .querySelector('label')
                                            .textContent
                                            .replace(/^\s*\*\s*|\s*:/g, '');

                if (campo.value != campo_confirmar.value) {
                    adicionarErro(campo, `O CAMPO ${label_campo} É DIFERENTE DO CAMPO ${label_campo_confirmar}`);  
                }
            }

            return;
        }

        function validarSelectECheckbox(campo)
        {
            if ((campo.type != 'select' && campo.type != 'checkbox')
            || !campo.hasAttribute('required')) {
                return;
            }

            let form = campo.closest('form');

            let label = form.querySelector(`*[name="${campo.name}"]`)
                        .closest('div')
                        .querySelector('label')
                        .textContent
                        .replace(/^\s*\*\s*|\s*:/g, '');

            if (campo.type === 'select' && !campo.selected) {
                adicionarErro(campo, `SELECIONE O CAMPO ${label}`); 
                return;
            }

            if (campo.type === 'checkbox' && !campo.checked) {
                adicionarErro(campo, `MARQUE O CAMPO ${label}`); 
            }
        }

        campos.forEach(campo => {
            msgErros[campo.name] = [];
            
            validarSelectECheckbox(campo);
            validarPorObjRegex(campo, campo.dataset.validacao);
            validarConfirmacaoCampos(campo);
        });

        return {
            'msgErros': msgErros,
            'tem_erro': tem_erro
        };
    }
});