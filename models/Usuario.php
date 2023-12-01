<?php
namespace models;

require_once __DIR__."/Conexao.php";
require_once __DIR__."/ValidacaoGeral.php";
require_once __DIR__."/Dispositivo.php";

use \models\Conexao;
use \models\ValidacaoGeral;
use \models\Dispositivo;
use \DateTime; 
use \PDO; 

class Usuario extends Conexao  
{        
    /**
     * login
     * REALIZAR LOGIN DO USUÁRIO
     * @param  string $email
     * @param  string $senha
     * @return bool
     */
    public function login(string $usuario_nome, string $senha): bool
    {
        $sql = "SELECT *
                FROM usuarios
                WHERE BINARY nome = :nome LIMIT 1";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":nome", $usuario_nome);
        $sql->execute();           

        if ($sql->rowCount() == 0) {
            return false;
            exit;
        }

        $dados_usuario = $sql->fetch(PDO::FETCH_ASSOC);

        //Verifica se as senhas batem e abre as sessões
        if (!password_verify($senha, $dados_usuario['senha'])) {
            return false;
            exit;
        }

        unset($_SESSION);
        session_destroy();

        //APAGAR AS SESSÕES EXISTENTES
        if(session_status() === PHP_SESSION_NONE){ 
            session_start();
        }

        $_SESSION['usuario']['id'] = intval($dados_usuario['id']);
        $_SESSION['usuario']['nome'] = $dados_usuario['nome'];

        $dispositivo = new Dispositivo();
        $dados_dispositivo = $dispositivo->pegarDispositivoUsuario(intval($dados_usuario['id']));
        
        if (count($dados_dispositivo) > 0) {
            $_SESSION['dispositivo']['id'] = intval($dados_dispositivo['id']);
            $_SESSION['dispositivo']['nome'] = $dados_dispositivo['nome'];
        }

        return true;
    }
    
    /**
     * logout
     * REALIZAR LOGOUT DO USUÁRIO
     * @return void
     */
    public function logout() :void
    {
        unset(
            $_SESSION['usuario'],
            $_SESSION['area_config']
        );

        header("Location: ".BASE."login");
    }
        
    /**
     * existe
     * VERIFICA SE O NOME DE USUÁRIO JÁ EXISTE
     * @param  string $nome
     * @return bool
     */
    public function existe(string $nome): bool
    {
        //BINARY VERIFICA CASE SENSITIVE
        $sql = "SELECT * FROM usuarios WHERE BINARY nome = :nome LIMIT 1";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':nome', $nome);
        $sql->execute();

        $dados_usuario = $sql->fetch();

        //CASO O NOME DE USUÁRIO EXISTA, MAS PERTENÇA A OUTRO USUÁRIO
        if ($sql->rowCount() === 1 
        && intval($dados_usuario['id']) != intval($_SESSION['usuario']['id'])) {
            return true;
            exit;
        }
        
        return false;
    }

    
    /**
     * atualizar
     * ATUALIZAR OS DADOS DO USUÁRIO
     * @param  int $usuario_id
     * @param  string $nome
     * @param  string $senha
     * @return bool
     */
    public function atualizar(int $usuario_id, string $nome, string $senha): bool
    {
        $senha_bd = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios 
                SET nome = :nome, senha = :senha
                WHERE id = :usuario_id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':nome', $nome);
        $sql->bindValue(':senha', $senha_bd);
        $sql->bindValue(':usuario_id', $usuario_id);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            return false;
            exit;
        }

        unset(
            $_SESSION['usuario'],
            $_SESSION['area_config']
        );
        
        return true;
    }
}