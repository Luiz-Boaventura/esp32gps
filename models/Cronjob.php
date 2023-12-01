<?php
namespace models;

require_once __DIR__."/Conexao.php";
require_once __DIR__."/Dispositivo.php";

use \models\Conexao;
use \models\Dispositivo;
use \DateTime; 
use \DateTimeZone; 
use \DateInterval;
use \PDO; 

class Cronjob extends Conexao  
{        
        
    /**
     * setarFimTrip
     * CONCLUI A TRIP, SETANDO FIM = 1, QUANDO NA COLUNA COORDENADA:
     * INICIO = 0
     * FIM = 0
     * DATA EM UTC NO BANCO SEJA MENOR DO QUE 15 MINUTOS DA DATA UTC ATUAL
     * @return void
     */
    public function setarFimTrip(): void
    {
        $dispositivo = new Dispositivo();
        $pegar_dispositivos = $dispositivo->pegarTodos();

        $quantidade_dispositivos = count($pegar_dispositivos);

        $sql = "SELECT c1.*
                FROM coordenadas c1
                JOIN (
                    SELECT id_dispositivo, MAX(data) AS max_data
                    FROM coordenadas
                    GROUP BY id_dispositivo
                ) c2
                ON c1.id_dispositivo = c2.id_dispositivo AND c1.data = c2.max_data
                WHERE inicio = 0 AND fim = 0
                LIMIT :limite";
        $sql = $this->db->prepare($sql);
        $sql->bindParam(':limite', $quantidade_dispositivos, PDO::PARAM_INT);
        $sql->execute();

        $dados_ultimas_coordenadas_dispositivos = $sql->fetchAll(PDO::FETCH_ASSOC);

        $arr_ids_atualizar = [];

        foreach ($dados_ultimas_coordenadas_dispositivos as $ultimas_coordenadas_dispositivos) {
            $data_bd = new DateTime($ultimas_coordenadas_dispositivos['data'], new DateTimeZone('UTC'));

            // Adicione 15 minutos à data do banco de dados
            $data_bd->add(new DateInterval('PT15M'));

            // Obtenha a data atual em UTC
            $data_atual = new DateTime('now', new DateTimeZone('UTC'));

            // Compare as datas
            // A data do banco de dados + 15 minutos é menor do que a data atual
            if ($data_bd < $data_atual) {
                array_push($arr_ids_atualizar, intval($ultimas_coordenadas_dispositivos['id']));
            }
        }

        if (count($arr_ids_atualizar) === 0) {
            return;
        }

        $ids_para_atualizar = implode(',', $arr_ids_atualizar);

        //ATUALIZAR PARA FIM = 1
        $sql = "UPDATE coordenadas 
                SET fim = 1
                WHERE id IN ($ids_para_atualizar)";
        $sql = $this->db->prepare($sql);
        $sql->execute();
    }
}