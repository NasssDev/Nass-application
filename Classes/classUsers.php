<?php

require_once('..\Fonctions\dbConnexion.php');
require_once('classLog.php');
require_once('classRoles.php');

class Users {

	private $id;
	private $updated_at;
	private $last_connexion_at;
	private $is_deleted;
	private $username;
	private $email;
	private $password;
	private $roleCode;

	private $obj_role;

	private $nbrUsers;
	private $db;
	private $name_table;
	private $errorss = [];
	private $userss = [];
	

	public function __construct($maconnexion="",$my_table="users") 
	{
		$this->db = $maconnexion;
		$this->name_table = $my_table;
	}

/*	public function __set($name, $value)
	{
        // empty
	}*/

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setUpdatedAt($updated)
	{
		$this->updated_at = $updated_at;
	}

	public function getUpdatedAt()
	{
		return $this->updated_at;
	}

	public function setLastConnexionAt($last_connexion_at)
	{
		$this->last_connexion_at = $last_connexion_at;
	}

	public function getLastConnexionAt()
	{
		return $this->last_connexion_at;
	}

	public function setIsDeleted($deleted)
	{
		$this->is_deleted = $deleted;
	}

	public function getIsDeleted()
	{
		return $this->is_deleted;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setPassword($password = NULL)
	{
		$this->password = $password;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getErrors()
	{
		return $this->errorss;
	}

	public function getUsers()
	{
		return $this->userss;
	}

	public function getNbrUsers()
	{
		return $this->nbrUsers;
	}

	public function setRoleCode($roleCode)
	{
		$this->roleCode = $roleCode;
	}

	public function getRoleCode()
	{
		return $this->roleCode;
	}

	public function setObjRole($objRole)
	{
		$this->obj_role = $objRole;
	}

	public function getObjRole()
	{
		return $this->obj_role;
	}


//-------------------------------------------------- CRUD --------------------------------------------------//


	public function select($use_deleted = true)
	{
		$sqlQuery = "SELECT id,created_at,updated_at,last_connexion_at,is_deleted,username,email,password,role_code FROM ".$this->name_table;

		if ($this->id != NULL || $this->username != NULL || $this->email != NULL) {
			$sqlQueryCond = "";

			if ($this->id != NULL) {
				$sqlQueryCond .= "id = :id";
			}
			if ($this->username != NULL) {
				if (!empty($sqlQueryCond)) $sqlQueryCond .= " OR ";
				$sqlQueryCond .= "username = :username";
			}
			if ($this->email != NULL) {
				if (!empty($sqlQueryCond)) $sqlQueryCond .= " OR ";
				$sqlQueryCond .= "email = :email";
			}
			if ($use_deleted) {
				$sqlQuery.= " WHERE is_deleted=false AND (".$sqlQueryCond.") ORDER BY id";
			} else {
				$sqlQuery.= " WHERE ".$sqlQueryCond." ORDER BY id";
			}
			/*"SELECT id,created_at,updated_at,last_connexion_at,is_deleted,username,email,password FROM users WHERE is_deleted=false AND (username = :username OR email = :email)";*/
			try {
				$sqlStatement = $this->db->prepare($sqlQuery);
				
				if ($this->id != NULL) {
					$sqlStatement->bindParam(':id', $this->id);
				}
				if ($this->username != NULL) {
					$sqlStatement->bindParam(':username', $this->username);
				}
				if ($this->email != NULL) {
					$sqlStatement->bindParam(':email', $this->email);
				}

				$sqlStatement->execute();

				$result = $sqlStatement->fetch();

				if ($result) {
					$this->id = $result['id'];
					$this->updated_at = $result['updated_at'];
					$this->last_connexion_at= $result['last_connexion_at'];
					$this->is_deleted= $result['is_deleted'];
					$this->username= $result['username'];
					$this->email= $result['email'];
					$this->password= $result['password'];
					$this->roleCode= $result['role_code'];
				} else {
					$this->id = 0;
				}
			} catch (PDOException $e) {
				$log = new Log();
				$log->general($e->getMessage());
				unset($log);

				$this->errorss[] = "contact your administrator !! (select)";
			}
		}
	}

	public function selectAll($limit=100,$offset=0,$tri="",$ascendant= true)
	{

		$sqlQuery = "SELECT a1.id as userId, email, username, last_connexion_at, is_deleted, a2.id as roleId, code, label FROM ".$this->name_table." a1 INNER JOIN roles a2 ON a1.role_code=a2.code";

		$sqlQueryCond = "";

		if ($this->id != NULL) {
			$sqlQueryCond .= "a1.id >= :id";
		}
		if ($this->username != NULL) {
			if (!empty($sqlQueryCond)) $sqlQueryCond .= " OR ";
			$sqlQueryCond .= "username LIKE CONCAT(:username,'%')";
		}
		if ($this->email != NULL) {
			if (!empty($sqlQueryCond)) $sqlQueryCond .= " OR ";
			$sqlQueryCond .= "email LIKE CONCAT(:email,'%')";
		}

		$sqlQuery.= " WHERE 1 ";
		if (!empty($sqlQueryCond)) {
			$sqlQuery .= "AND (".$sqlQueryCond.") ";
		}
		switch ($tri) {
			case 'username':
			$tri = "username";
			break;
			case 'email':
			$tri = "email";
			break;
			case 'last_connexion_at':
			$tri = "last_connexion_at";
			break;
			case 'fk_role_code':
			$tri = "fk_role_code";
			break;
			case 'is_deleted':
			$tri = "is_deleted";
			break;
			default:
			$tri = "userId";
			break;
		}
		$ordre = "ASC";
		if (!$ascendant) {
			$ordre = "DESC";
		}
		$sqlQuery .= " ORDER BY ".$tri." ".$ordre;
		$sqlQuery .= " LIMIT ".$limit." OFFSET ".$offset;
		echo($sqlQuery);
		try {
			$sqlStatement = $this->db->prepare($sqlQuery);
			if ($this->id != NULL) {
				$sqlStatement->bindParam(':id', $this->id);
			}
			if ($this->username != NULL) {
				$sqlStatement->bindParam(':username', $this->username);
			}
			if ($this->email != NULL) {
				$sqlStatement->bindParam(':email', $this->email);
			}
			$sqlStatement->execute();
			/*$this->payss = $sqlStatement->fetchAll();*/
			$results = $sqlStatement->fetchAll();

			foreach ($results as $result) {
				$user = new Users($this->db);
				$user->setId($result["userId"]);
				$user->setUsername($result["username"]);
				$user->setEmail($result["email"]);
				$user->setLastConnexionAt($result["last_connexion_at"]);
				$user->setIsDeleted($result["is_deleted"]);
				$user->setRoleCode($result["code"]);

				$role = new Roles($this->db);
				$role->setId($result["roleId"]);
				$role->setLabel($result["label"]);
				$role->setCode($result["code"]);

				$user->setObjRole($role);
				unset($role);
				$this->userss[] = $user;
			}

		} catch(PDOException $e){

			$this->errorss[] = "Erreur 404 contactez l'administrateur !";
			$log = new log();
			$log->general($e->getMessage());
			unset($log);
		}
	}

	public function selectCount($tri="")
	{

		$sqlQuery = "SELECT COUNT(*) AS nbUsers FROM ".$this->name_table;

		$sqlQueryCond = "";

		if ($this->id != NULL) {
			$sqlQueryCond .= "id >= :id";
		}
		if ($this->username != NULL) {
			if (!empty($sqlQueryCond)) $sqlQueryCond .= " OR ";
			$sqlQueryCond .= "username LIKE CONCAT(:username,'%')";
		}
		if ($this->email != NULL) {
			if (!empty($sqlQueryCond)) $sqlQueryCond .= " OR ";
			$sqlQueryCond .= "email LIKE CONCAT(:email,'%')";
		}

		$sqlQuery.= " WHERE 1 ";
		if (!empty($sqlQueryCond)) {
			$sqlQuery .= "AND (".$sqlQueryCond.") ";
		}
		switch ($tri) {
			case 'username':
			$tri = "username";
			break;
			case 'email':
			$tri = "email";
			break;
			case 'last_connexion_at':
			$tri = "last_connexion_at";
			break;
			case 'fk_role_code':
			$tri = "fk_role_code";
			break;
			case 'is_deleted':
			$tri = "is_deleted";
			break;
			default:
			$tri = "id";
			break;
		}
		$sqlQuery .= " ORDER BY ".$tri;
		echo($sqlQuery);
		try {
			$sqlStatement = $this->db->prepare($sqlQuery);

			if ($this->id != NULL) {
				$sqlStatement->bindParam(':id', $this->id);
			}
			if ($this->username != NULL) {

				$sqlStatement->bindParam(':username', $this->username);
			}
			if ($this->email != NULL) {
				$sqlStatement->bindParam(':email', $this->email);
			}

			$sqlStatement->execute();

			$result = $sqlStatement->fetch();
			$this->nbrUsers= (int)$result['nbUsers'];

            // $userPage=5;
            // $pageTotal=ceil($nbrUsers / $userPage);
            // echo $pageTotal;

		} catch (PDOException $e) {
            // error_log($e->getMessage(), 3, "logPerso.log");
			$log = new Log();
			$log->general($e->getMessage());
			unset($log);

			$this->erreurs[] = "Contacter votre administrateur du site ";
		}
	}


//-------------------------------------------------- UPDATE --------------------------------------------------//

	public function update($use_deleted=false) {
		if ($this->id != NULL && $this->username != NULL && $this->email != NULL) {

			//On verifie que l'enregistrement en cours existe bien
			$sqlQuery = 'SELECT id FROM '.$this->name_table .' WHERE id = :id';
			if ($use_deleted){
				' AND is_deleted = false';
			}
			//echo($sqlQuery);

			try {
				$sqlStatement = $this->db->prepare($sqlQuery);
				$sqlStatement->execute([
					'id' => $this->id
				]);

				$result = $sqlStatement->fetch();
				//Si le fetch ne retourne aucune ligne, il vaut false.
				if ($result) {
					//L'enregistrement existe. On peut poursuivre le process

					//On verifie qu'il n'existe aucun enregistrement avec le même userName ou le même Email autre que celui-en cours
					$sqlQuery = 'SELECT id, email, username FROM '.$this->name_table .' WHERE id <> :id AND is_deleted = false AND (username = :username OR email = :email)';

					$sqlStatement = $this->db->prepare($sqlQuery);
					$sqlStatement->execute([
						':id' => $this->id,
						':username' => $this->username,
						':email' => $this->email
					]);
					$result = $sqlStatement->fetch();

					//Si le fetch ne retourne aucune ligne, il vaut false
					if ($result) {
						if($result['username'] == $this->username) {
							$this->errorss[] = "Un enregistrement avec ce nom d'utilisateur ou cet email existe";
						}
						if ($result['email'] == $this->email) {
							$this->errorss[] = "Un enregistrement avec cet email existe";
						}
					}
					else {
						//Il n'existe aucun autre enregistrement que celui en cours avec le même Email ou le même UserName => On peut faire l'Update

						$sqlQuery = 'UPDATE '.$this->name_table.' SET updated_at= CURRENT_TIMESTAMP, username= :username, email= :email';
						if ($this->password != NULL){
							$sqlQuery .= ', password= :password';
						}
						if ($this->roleCode != NULL){
							$sqlQuery .= ', role_code= :role_code';
						}
						$sqlQuery .=' WHERE id = :id';

						/*echo($sqlQuery.' '.$this->id);*/

						$sqlStatement = $this->db->prepare($sqlQuery);

						$sqlStatement->bindParam(':id', $this->id);
						$sqlStatement->bindParam(':username', $this->username);
						$sqlStatement->bindParam(':email', $this->email);

						if ($this->password != NULL) {

							$this->password = password_hash($this->password, PASSWORD_DEFAULT);
							$sqlStatement->bindParam(':password', $this->password);
						}
						if ($this->roleCode != NULL) {

							$sqlStatement->bindParam(':role_code', $this->roleCode);
						}
						$sqlStatement->execute();
					}
				}
				else {
					$this->errorss[] = "Cet enregistrement n'existe pas : ".$this->id;
				}
			}
			catch(PDOException $e){
				$this->errorss[] = "Erreur imprévue lors de la sélection. Réessayez plus tard ou contacter l'administrateur";

				$log = new log();
				$log->general($e->getMessage());
				unset($log);
			}
		}
	}



	public function update_nass()
	{
		if ($this->id != NULL && $this->email != NULL && $this->password != NULL) {
			// On créer des variables(temporaire) pour conserver les valeurs de $this temporairement
			$email_tmp = $this->email;
			$username_tmp = $this->username;
			$password_tmp = $this->password;

			// On vide les variable $this suivante pour effectuer un select uniquement sur l'id
			$this->email="";
			$this->username="";

			$this->select();

			/*Si en faisant un select a partir de l'id on trouve un enregistrement existant 
			alors $this->id sera différent de zero*/

			if ($this->id > 0 ) {

				// on recupere $this->id dans une variable temporaire et on vide la variable $this->id

				$id_tmp = $this->id;
				$this->id="";

				/* on repopule les variables $this suivantes avec les valeurs conservées dans les variables temporaires
				cette fois on fait un select uniquement sur l'email et le username */

				if(strtolower($this->email) == strtolower($email_tmp)) {
					$this->email ="";
				} else {
					$this->email = $email_tmp;
				}
				if (strtolower($this->username) == strtolower($username_tmp)) {
					$this->username = "";
				} else {
					$this->username = $username_tmp;
				}

				$this->select();
			}
			echo("<h1 style='color : red'>".$this->id."</h1><br>");
			echo("<h1 style='color : red'>".$id_tmp."</h1><br>");
			if ($this->id == 0 || $this->id == $id_tmp) {

				$this->id = $id_tmp;
				$this->email = $email_tmp;
				$this->username = $username_tmp;
				$this->password = $password_tmp;

				$sqlQuery = "UPDATE ".$this->name_table." SET updated_at = CURRENT_TIMESTAMP, username = :username, email = :email, password = :password WHERE id = :id";
				try {
					$sqlStatement = $this->db->prepare($sqlQuery);
					$sqlStatement->execute([
						':id' => $this->id,
						':email' => $this->email,
						':username' => $this->username,
						':password' => password_hash($this->password, PASSWORD_DEFAULT)
					]);
				} catch (PDOException $e) {
					$log = new Log();
					$log->general($e->getMessage());
					unset($log);
					$this->errorss[] = "Erreur 404 contactez l'administrateur !";
				}
			} else {
				echo "je fonctionne pas";
			}
		} else {
			echo "La modif n'a pas fonctionner";
		}
	}

//-------------------------------------------------- UPDATE COLUMN --------------------------------------------------//


	public function updateColumn($context)
	{

		if ($this->id != NULL) {
			$sqlQuery = "SELECT id FROM ".$this->name_table." WHERE id = :id";

			try {	
				$sqlStatement = $this->db->prepare($sqlQuery);
				$sqlStatement->execute([
					':id' => $this->id
				]);

				if ($this->id > 0) {

					if (in_array($context, ["is_deleted", "last_connexion", "is_activated"])){
						switch ($context) {

							case 'is_deleted':
							$sqlQuery = "UPDATE ".$this->name_table." SET is_deleted = true, deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
							break;

							case 'last_connexion' :
							$sqlQuery = "UPDATE ".$this->name_table." SET last_connexion_at = CURRENT_TIMESTAMP WHERE id = :id";
							break;

							case 'is_activated':
							$sqlQuery = "UPDATE ".$this->name_table." SET is_deleted = false, updated_at = CURRENT_TIMESTAMP, deleted_at = NULL WHERE id = :id";
							break;

							default:

							break;
						}

						$sqlStatement = $this->db->prepare($sqlQuery);
						$sqlStatement->execute([
							':id' => $this->id
						]);
					}else {
						$log = new Log();
						$log->general("Problème dans classeUsers => fonction updateColumn()");
						unset($log);
						$this->erreurs[] = "Erreur 404 contactez l'administrateur !";
					}
				} 
			} catch (PDOException $e) {
				$log = new Log();
				$log->general($e->getMessage());
				unset($log);
				$this->erreurs[] = "Erreur 404 contactez l'administrateur !";
			}
		}
	}


//-------------------------------------------------- INSERT --------------------------------------------------//


	public function insert() 
	{
		if ($this->username != NULL && $this->email != NULL && $this->password != NULL) {
			$this->select();
			if ($this->id == 0) {
				$sqlQuery = "INSERT INTO ".$this->name_table."(is_deleted, username, email, password) VALUES (false, :username, :email, :password)";

				try {
					$sqlStatement = $this->db->prepare($sqlQuery);
					$sqlStatement->execute([
						":username" => $this->username,
						":email" => $this->email,
						":password" => password_hash($this->password, PASSWORD_DEFAULT)
					]);
					$this->id = $this->db->lastInsertId();
				} catch(PDOException $e) {
					$log = new Log();
					$log->general($e->getMessage());
					unset($log);

					$this->errorss[] = "data already exist";
				}
			} else {
				$this->id = 0;
				$this->errorss[] = "<h1 style='color : red'>data already exist </h1><br>";
			}
		}
	}


}
/*
public function update() {
		if ($this->id != NULL && $this->username != NULL && $this->password != NULL && $this->email != NULL) {
			//On verifie que l'enregistrement en cours existe bien
			$sqlQuery = 'SELECT id FROM '.$this->name_table .' WHERE id = :id AND is_deleted = false';

			//echo($sqlQuery);

			try {
				$sqlStatement = $this->db->prepare($sqlQuery);
				$sqlStatement->execute([
					'id' => $this->id
				]);
				
				$result = $sqlStatement->fetch();
				//Si le fetch ne retourne aucune ligne, il vaut false.
				if ($result) {
					//L'enregistrement existe. On peut poursuivre le process

					//On verifie qu'il n'existe aucun enregistrement avec le même userName ou le même Email autre que celui-en cours
					$sqlQuery = 'SELECT id FROM '.$this->name_table .' WHERE id <> :id AND is_deleted = false AND (username = :username OR email = :email)';

					$sqlStatement = $this->db->prepare($sqlQuery);
					$sqlStatement->execute([
						':id' => $this->id,
						':username' => $this->username,
						':email' => $this->email
					]);

					$result = $sqlStatement->fetch();
					//Si le fetch ne retourne aucune ligne, il vaut false
					if ($result) {
						$this->errorss[] = "Un enregistrement avec ce nom d'utilisateur ou cet email existe";
					}
					else {
						//Il n'existe aucun autre enregistrement que celui en cours avec le même Email ou le même UserName => On peut faire l'Update
						$sqlQuery = 'UPDATE '.$this->name_table.' SET updated_at= CURRENT_TIMESTAMP, username= :username, email= :email, password= :password WHERE id = :id';
						//echo($sqlQuery.' '.$this->code);

						$sqlStatement = $this->db->prepare($sqlQuery);
						$sqlStatement->execute([
							':id' => $this->id, //On peut utiliser le $this car cette proprièté n'a pas été affectée par le Select
							':username' => $this->username,
							':email' => $this->email,
							':password' => password_hash($this->password, PASSWORD_DEFAULT)
						]);
					}
				}
				else {
					$this->errorss[] = "Cet enregistrement n'existe pas : ".$this->id;
				}
			}
			catch(PDOException $e){
				$this->errorss[] = "Erreur imprévue lors de la sélection. Réessayez plus tard ou contacter l'administrateur";
				
				$log = new log();
				$log->general($e->getMessage());
				unset($log);
			}
		}
	}
	$user = new Users($db);
	$user->selectAll("last_connexion_at");
	echo "<pre>";
	print_r($user->getUsers());
	print_r($user->getErrors());
	echo "</pre>";*/
?>