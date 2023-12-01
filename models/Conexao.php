<?php
namespace models;

use \PDO;

class Conexao  
{
	protected $db;
	private $db_connect;
	private $driver = null;
	private $host = null;
	private $database = null;
	private $user = null;
	private $pass = null;
	private $charset = null;
	
	/**
	 * __construct
	 * REALIZAR O ENCAPSULAMENTO DA CONEXÃO DO ATRIBUTO "db_connect"
	 * PARA O ATRIBUTO "db"
	 * @return void
	 */
	public function __construct() 
	{
		$this->getConexao();
		$this->db = $this->db_connect; 
	}
	
	/**
	 * getConexao
	 * REALIZAR A CONEXÃO COM O PDO
	 * @return void
	 */
	private function getConexao(): void
	{
		$this->driver = 'mysql';
		$this->host = 'localhost';
		$this->database = 'esp32gps';
		$this->user = 'root';
		$this->pass = 'xxxxxxxxxxx';
		$this->charset =  'utf8';

		try
		{
			$this->db_connect = new PDO(
				$this->driver.":dbname=".$this->database.";host=".$this->host.";charset=".$this->charset, 
				$this->user, 
				$this->pass
			);
			$this->db_connect->setAttribute(
				PDO::ATTR_ERRMODE,
				PDO::ERRMODE_EXCEPTION
			);
		}
		catch(PDOException $e)
		{
			$msg = "Driver disponíveis: ".implode(",", PDO::getAvailableDrivers());
			$msg .= "\nErro: ". $e->getMessage();

			throw new PDOException($e);
			throw new Exception($msg);
		} 
	}
}