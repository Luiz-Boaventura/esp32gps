<?php
namespace models;

require_once __DIR__."/Conexao.php";

use \models\Conexao;
use \PDO; 

class Dispositivo extends Conexao  
{            
    /**
     * pegarDispositivo
     * RETORNA OS DADOS DO DISPOSITIVO A PARTIR DO NOME DO DISPOSITIVO
     * @param  string $dispositivo_nome
     * @return bool|array
     */
    public function pegarDispositivo(string $dispositivo_nome): bool|array
    {
        $sql = "SELECT * FROM dispositivos 
                WHERE nome = :nome
                LIMIT 1";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':nome', $dispositivo_nome);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            return false;
            exit;
        }

        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * pegarDispositivoPeloId
     * RETORNA OS DADOS DO DISPOSITIVO A PARTIR DO ID DO DISPOSITIVO
     * @param  int $dispositivo_id
     * @return array
     */
    public function pegarDispositivoPeloId(int $dispositivo_id): array
    {
        $sql = "SELECT * FROM dispositivos 
                WHERE id = :dispositivo_id
                LIMIT 1";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':dispositivo_id', $dispositivo_id);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            return [];
            exit;
        }

        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * pegarDispositivoUsuario
     * RETORNA OS DADOS DO DISPOSITIVO A PARTIR DO ID DO USUÁRIO
     * @param  int $usuario_id
     * @return array
     */
    public function pegarDispositivoUsuario(int $usuario_id): array
    {
        $sql = "SELECT d.id, d.nome 
                FROM usuario_dispositivo AS ud
                INNER JOIN dispositivos AS d ON d.id = ud.id_dispositivo
                WHERE ud.id_usuario = :usuario_id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':usuario_id', $usuario_id);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            return [];
            exit;
        }

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    
    /**
     * pegarTodos
     * RETORNA TODOS OS DISPOSITIVOS COM SEUS RESPECTIVOS DADOS
     * @return array
     */
    public function pegarTodos(): array
    {
        $sql = "SELECT * FROM dispositivos";
        $sql = $this->db->prepare($sql);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            return [];
            exit;
        }

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * cadastrar
     * CADASTRAR UM DISPOSITIVO
     * @param  string $dispositivo_nome
     * @return bool|int
     */
    public function cadastrar(string $dispositivo_nome): bool|int
    {
        //CADASTRAR DISPOSITIVO
        $sql = "INSERT INTO dispositivos SET nome = :nome";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":nome", $dispositivo_nome);
        $sql->execute();           

        //ERRO AO CADASTRAR
        if ($sql->rowCount() == 0) {
            return false;
            exit;
        }

        return intval($this->db->lastInsertId());
    }
    
    /**
     * trocarDispositivoUsuario
     * TROCAR O DISPOSITIVO EXIBIDO AO USUÁRIO NO SISTEMA
     * @param  int $usuario_id
     * @param  int $dispositivo_id
     * @return bool
     */
    public function trocarDispositivoUsuario(int $usuario_id, int $dispositivo_id): bool
    {
        $pegar_dispositivo_usuario = $this->pegarDispositivoUsuario($usuario_id);
        
        if (count($pegar_dispositivo_usuario) > 0) {
            $sql = "UPDATE usuario_dispositivo 
                    SET id_dispositivo = :dispositivo_id
                    WHERE id_usuario = :usuario_id";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(':dispositivo_id', $dispositivo_id);
            $sql->bindValue(':usuario_id', $usuario_id);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return false;
                exit;
            }

            unset($_SESSION['dispositivo']);

            $dados_dispositivo = $this->pegarDispositivoPeloId($dispositivo_id);

            $_SESSION['dispositivo']['id'] = intval($dados_dispositivo['id']);
            $_SESSION['dispositivo']['nome'] = $dados_dispositivo['nome'];
            
            return true;          
            exit;
        }

        //CADASTRAR DISPOSITIVO
        $sql = "INSERT INTO usuario_dispositivo 
                SET id_usuario = :usuario_id, id_dispositivo = :dispositivo_id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":usuario_id", $usuario_id);
        $sql->bindValue(":dispositivo_id", $dispositivo_id);
        $sql->execute();           

        //ERRO AO CADASTRAR
        if ($sql->rowCount() == 0) {
            return false;
            exit;
        }

        $dados_dispositivo = $this->pegarDispositivoPeloId($this->db->lastInsertId());

        unset($_SESSION['dispositivo']);

        $_SESSION['dispositivo']['id'] = intval($dados_dispositivo['id']);
        $_SESSION['dispositivo']['nome'] = $dados_dispositivo['nome'];

        return true;
    }
}