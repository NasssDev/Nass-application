<?php


require_once('..\Fonctions\dbConnexion.php');
require_once('classLog.php');

class Roles {

	private $id;
	private $code;
	private $label;

	private $db;
	private $name_table;
	private $errorss = [];
	private $roless = [];

	public function __construct($maconnexion="",$my_table="roles") 
	{
		$this->db = $maconnexion;
		$this->name_table = $my_table;
	}
	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setLabel($label)
	{
		$this->label = $label;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function getRoless()
	{
		return $this->roless;
	}


	public function getErrors()
	{
		return $this->errorss;
	}

	public function selectAll()
	{

		$sqlQuery = "SELECT * FROM ".$this->name_table." WHERE 1 ORDER BY label";

		
		try {
			$sqlStatement = $this->db->prepare($sqlQuery);
			$sqlStatement->execute();
			/*$this->payss = $sqlStatement->fetchAll();*/
			$results = $sqlStatement->fetchAll();

			foreach ($results as $result) {
				$role = new Roles($this->db);
				$role->setId($result["id"]);
				$role->setLabel($result["label"]);
				$role->setCode($result["code"]);

				$this->roless[] = $role;
				unset($role);
			}

		} catch(PDOException $e){

			$this->errorss[] = "Erreur 404 contactez l'administrateur ! (Role)";
			$log = new log();
			$log->general($e->getMessage());
			unset($log);
		}
	}
}
?>