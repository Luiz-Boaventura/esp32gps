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

class Gps extends Conexao  
{        
    /**
     * cadastrar
     * CADASTRAR COORDENADAS E DISPOSITVO (CASO NÃO ESTEJA CADASTRADO)
     * USADO NA API DE CADASTRAR LOCALIZAÇÃO (api/localizacao/cadastrar)
     * @param  string $dispositivo_nome
     * @param  array $info
     * @return bool
     */
    public function cadastrar(string $dispositivo_nome, array $info): bool
    {
        $dispositivo = new Dispositivo();
        
        $pegar_dispositivo = $dispositivo->pegarDispositivo($dispositivo_nome);

        //CADASTRAR DISPOSITIVO, POIS NÃO EXISTE NO BANCO DE DADOS
        if ($pegar_dispositivo === false) {
            $dispositivo_id = $dispositivo->cadastrar($dispositivo_nome);
        } else {
            $dispositivo_id = intval($pegar_dispositivo["id"]);
        }

        //DEFINE INICIO TRIP = 1 OU 0
        $inicio_trip = $this->iniciarTrip($dispositivo_id);

        foreach($info as $key => $value)
        {
            //TRNASFORMAR DATA
            $data = str_split($info[$key][2], 2);
            $data = implode("/", $data);

            //TRANSFORMAR HORA
            $hora = str_split($info[$key][3], 2);
            $hora = implode(":", $hora);

            $data_hora_string = $data." ".$hora;

            $data_hora = DateTime::createFromFormat("d/m/y H:i:s", $data_hora_string);
            $data_hora_formatada = $data_hora->format('Y-m-d H:i:s');

            //CADASTRAR COORDENADA
            $sql = "INSERT INTO coordenadas 
                    SET id_dispositivo = :id_dispositivo, latitude = :latitude, longitude = :longitude, 
                    velocidade = :velocidade, inicio = :inicio, fim = :fim, data = :data";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(":id_dispositivo", $dispositivo_id);
            $sql->bindValue(":latitude", $info[$key][0]);
            $sql->bindValue(":longitude", $info[$key][1]);
            $sql->bindValue(":velocidade", $info[$key][4]);
            $sql->bindValue(":inicio", $inicio_trip);
            $sql->bindValue(":fim", 0);
            $sql->bindValue(":data", $data_hora_formatada);        
            $sql->execute();       

            if ($sql->rowCount() == 0) {
                return false;
            }

            $inicio_trip = 0;
        }

        return true;
    }
    
    /**
     * pegarFimTripDispositivo
     * PEGA O ÚLTIMO REGISTRO (ÚLTIMA INSERÇÃO NO ID_DISPOSITIVO) DO DISPOSITIVO ONDE FIM = 1
     * @param  int $dispositivo_id
     * @return array
     */
    private function pegarFimTripDispositivo(int $dispositivo_id): array
    {
        $sql = "SELECT id FROM coordenadas 
                WHERE id_dispositivo = :id_dispositivo AND fim = :fim 
                ORDER BY id DESC 
                LIMIT 1";        
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id_dispositivo', $dispositivo_id);
        $sql->bindValue(':fim', 1);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            return [];
            exit;
        }

        return $sql->fetch();
    }
    
    /**
     * iniciarTrip
     * RERTORNA O INICÍO DA TRIP COMO 1, CASO NÃO EXISTA REGISTROS PARA O ID DO DISPOSITIVO
     * OU CASO EXISTA FIM = 1 NO ÚLTIMO REGISTRO DO DISPOSITIVO
     * RETORNA INICIO DA TRIP COMO 0 CASO CONTRÁRIO.
     * @param  int $dispositivo_id
     * @return int
     */
    private function iniciarTrip(int $dispositivo_id): int
    {
        //VERIFICA SE EXISTE UMA COORDENADA CADASTRADA NO BANCO PARA O DISPOSITIVO
        $sql = "SELECT id FROM coordenadas 
                WHERE id_dispositivo = :id_dispositivo LIMIT 1";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id_dispositivo', $dispositivo_id);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            return 1;
            exit;
        }

        $existe_fim_trip = $this->pegarFimTripDispositivo($dispositivo_id);

        if (count($existe_fim_trip) > 0) {
            return 1;
            exit;
        }

        return 0;
    }
    
    /**
     * pegarTodasCoordenadasDoDia
     * MONTA O MAPA COM AS INFORMAÇÕES DE UM DISPOSITIVO, CASO NENHUM SEJA PASSADO
     * ADOTA-SE DISPOSITIVO COM ID = 1 COMO O DISPOSITIVO A SER CONSULTADO
     * @param  string $dispositivo
     * @return array
     */
    public function pegarTodasCoordenadasDoDia(int $dispositivo_id): array
    {
        $sql = "SELECT c.id, c.latitude, c.longitude, c.velocidade, c.inicio, c.fim,
                        c.data AS data_bd, 
                        DATE_FORMAT(STR_TO_DATE(c.data, '%Y-%m-%d %H:%i:%s'), '%d/%m/%Y %H:%i:%s') 
                        AS data_formatada_utc,
                        DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(c.data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-03:00'), '%d/%m/%Y %H:%i:%s') 
                        AS data_formatada_local,
                        d.id AS dispositivo_id, d.nome AS dispositivo_nome
                FROM coordenadas AS c 
                INNER JOIN dispositivos AS d ON d.id = c.id_dispositivo
                WHERE d.id = :dispositivo_id
                AND CONVERT_TZ(c.data, '+00:00', '-03:00') >= CONCAT(DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')), ' 00:00:00')
                AND CONVERT_TZ(c.data, '+00:00', '-03:00') <= CONCAT(DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')), ' 23:59:59')";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':dispositivo_id', $dispositivo_id);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            return [];
            exit;
        }

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * pegarCoordenadasDoDiaAtualizadas
     * RETORNA TODAS AS COORDENADAS DO DIA A PARTIR DE UM ID DE COORDENADA
     * OU TODAS AS COORDENADAS DO DIA
     * UTILIZADO NO FETCH API, PARA REALIZAR A CONSULTA ASSÍNCRONA DE INFORMAÇÕES
     * @param  int $dispositivo_id
     * @param  int|null $ultimo_id_viagem_atual
     * @return array
     */
    public function pegarCoordenadasDoDiaAtualizadas(int $dispositivo_id, int|null $ultimo_id_viagem_atual): array
    {
        if (is_null($ultimo_id_viagem_atual)) {
            $sql = "SELECT  id, latitude, longitude, velocidade, inicio, fim, data AS data_bd, 
                        DATE_FORMAT(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '%d/%m/%Y %H:%i:%s') 
                        AS data_formatada_utc,
                        DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-03:00'), '%d/%m/%Y %H:%i:%s') 
                        AS data_formatada_local
                    FROM coordenadas 
                    WHERE id_dispositivo = :dispositivo_id 
                        AND CONVERT_TZ(data, '+00:00', '-03:00') >= CONCAT(DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')), ' 00:00:00')
                        AND CONVERT_TZ(data, '+00:00', '-03:00') <= CONCAT(DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')), ' 23:59:59')
                    ORDER BY id ASC";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(':dispositivo_id', $dispositivo_id);
            $sql->execute();    
        } else {
            $sql = "SELECT  id, latitude, longitude, velocidade, inicio, fim, data AS data_bd, 
                        DATE_FORMAT(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '%d/%m/%Y %H:%i:%s') 
                        AS data_formatada_utc,
                        DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-03:00'), '%d/%m/%Y %H:%i:%s') 
                        AS data_formatada_local
                    FROM coordenadas 
                    WHERE id_dispositivo = :dispositivo_id 
                        AND id > :ultimo_id_viagem_atual 
                        AND CONVERT_TZ(data, '+00:00', '-03:00') >= CONCAT(DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')), ' 00:00:00')
                        AND CONVERT_TZ(data, '+00:00', '-03:00') <= CONCAT(DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')), ' 23:59:59')
                    ORDER BY id ASC";

            $sql = $this->db->prepare($sql);
            $sql->bindValue(':dispositivo_id', $dispositivo_id);
            $sql->bindValue(':ultimo_id_viagem_atual', $ultimo_id_viagem_atual);
            $sql->execute();
        }

        if ($sql->rowCount() === 0) {
            return [];
            exit;
        }

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * mostrarDatasViagensDispositivo
     * MOSTRA AS DATAS EM QUE O DISPOSITIVO FEZ VIAGENS (TRAZENDO SOMENTE AS DATAS DE FORMA ORDENADA NO ARRAY)
     * @param  int $dispositivo_id
     * @param  string $ano
     * @return array
     */
    public function mostrarDatasViagensDispositivo(int $dispositivo_id, string $ano): array
    {
        $sql = "SELECT 
                    DATE_FORMAT(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '%Y-%m-%d') AS data_bd,
                    DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-03:00'), '%d/%m/%Y') AS data_formatada_local,
                    DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-00:00'), '%Y-%m-%d %H:%i:%s') AS data_hora_formatada_bd,
                    DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-03:00'), '%d/%m/%Y %H:%i:%s') AS data_hora_formatada_local
                FROM coordenadas 
                WHERE id_dispositivo = :dispositivo_id
                GROUP BY data_formatada_local
                ORDER BY MAX(data) DESC";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':dispositivo_id', $dispositivo_id);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            return [];
            exit;
        }

        //REMONTAR ARRAY DE RETORNO [ANO] => [DATAS]
        $arr_datas = [];
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $ano_anterior = null;
        $x = 0;
        foreach ($dados as $dado) {
            $data_explode = explode("/", $dado['data_formatada_local']);
            $dia = intval($data_explode[0]);
            $mes = intval($data_explode[1]);
            $ano = intval($data_explode[2]);

            if (is_null($ano_anterior)) {
                $ano_anterior = $ano;
            }

            if ($ano_anterior != $ano) {
                $ano_anterior = $ano;
                $x = 0;
            }

            $arr_datas[$ano][$x] = $dado;
            $x++;
        }

        return $arr_datas;
    }
    
    /**
     * mostrarViagens
     * MOSTRA AS VIAGENS DO DISPOSITIVO SELECIONADO PELO USUÁRIO
     * @param  int $dispositivo_id
     * @param  string $data_br
     * @return array
     */
    public function mostrarViagens(int $dispositivo_id, string $data_br): array
    {
        $data_br_inicio = $data_br." 03:00:00";
        $data_inicio = DateTime::createFromFormat("d/m/Y H:i:s", $data_br_inicio);
        $data_inicio = $data_inicio->format('Y-m-d H:i:s');

        $data_fim = DateTime::createFromFormat("d/m/Y H:i:s", $data_br_inicio);
        $data_fim = $data_fim->modify('+1 day');
        $data_fim = $data_fim->setTime(2, 59, 59);
        $data_fim = $data_fim->format('Y-m-d H:i:s');

        $sql = "SELECT 
                    id,
                    latitude,
                    longitude,
                    velocidade,
                    inicio,
                    fim,
                    DATE_FORMAT(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '%d/%m/%Y %H:%i:%s') AS data_bd,
                    DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(data, '%Y-%m-%d %H:%i:%s'), '+00:00', '-03:00'), '%d/%m/%Y %H:%i:%s') AS data_formatada_local
                FROM coordenadas 
                WHERE id_dispositivo = :dispositivo_id 
                AND data >= :data_inicio AND data <= :data_fim
                ORDER BY id ASC";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':dispositivo_id', $dispositivo_id);
        $sql->bindValue(':data_inicio', $data_inicio);
        $sql->bindValue(':data_fim', $data_fim);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            return [];
            exit;
        }

        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $arr = [];
        $viagem_numero = 0;
        $coordenadas_numero = 0;

        foreach ($dados as $key => $dado) {
            $arr[$viagem_numero][$coordenadas_numero]['id'] = intval($dado['id']);
            $arr[$viagem_numero][$coordenadas_numero]['latitude'] = floatval($dado['latitude']);
            $arr[$viagem_numero][$coordenadas_numero]['longitude'] = floatval($dado['longitude']);
            $arr[$viagem_numero][$coordenadas_numero]['velocidade'] = floatval($dado['velocidade']);
            $arr[$viagem_numero][$coordenadas_numero]['inicio'] = intval($dado['inicio']);
            $arr[$viagem_numero][$coordenadas_numero]['fim'] = intval($dado['fim']);
            $arr[$viagem_numero][$coordenadas_numero]['data_bd'] = $dado['data_bd'];
            $arr[$viagem_numero][$coordenadas_numero]['data_formatada_local'] = $dado['data_formatada_local'];

            $coordenadas_numero++;

            if (intval($dado['fim']) === 1
            || (intval(end($dados)['id']) === $dado['id'] && intval($dado['fim']))) {
                $coordenadas_numero = 0;
                $viagem_numero++;
            }
        }

        return $arr;
    }
}