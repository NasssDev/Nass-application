<?php 

session_start();

require_once("../Fonctions/dbConnexion.php");
require_once("../Classes/classUsers.php");
require_once("../Classes/classRoles.php");

echo "<pre>";
print_r($_REQUEST);
echo "<pre>";

$errors = array();
$role = new Roles($db);
$role->selectAll();
$tabRoles = $role->getRoless();
$errors = array_merge($errors, $role->getErrors());

if (isset($_REQUEST['submit'])) {

	$idOld= array();
	$idNew= array();

	foreach ($_REQUEST as $key => $value) {
		
		$posDeleteNew = strpos($key, "deleteNew");
		$posDeleteOld = strpos($key, "deleteOld");

		if ($posDeleteNew !== false) {
			array_push($idNew, $value);
		}		
		if ($posDeleteOld !== false) {
			array_push($idOld, $value);
		}
	}

	$doDeletedTrue= array_diff($idNew, $idOld);
	$doDeletedFalse= array_diff($idOld, $idNew);


	foreach ($doDeletedTrue as $id ) {
		$user = new Users($db);
		$user->setId($id);
		$user->updateColumn("is_deleted");
		$errors = array_merge($errors, $user->getErrors());
		unset($user);
	}
	foreach ($doDeletedFalse as $id ) {
		$user = new Users($db);
		$user->setId($id);
		$user->updateColumn("is_activated");
		$errors = array_merge($errors, $user->getErrors());
		unset($user);
	}

	foreach ($_REQUEST as $key => $value) {

		$posRoleUser = strpos($key, "roleUser_");
		
		if ($posRoleUser !== false) {

			$id = str_replace("roleUser_", "", $key);
			$user = new Users($db);
			$user->setId($id);
			$user->select(false);
			if($user->getId() == $id) {
				if ($user->getRoleCode() != $value)
				$user->setRoleCode($value);
				$user->update(false);
				$errors = array_merge($errors, $user->getErrors());
				unset($user);
			}
		}
	}
}

$user = new Users($db);
if (isset($_REQUEST['tri'])) {

	$_REQUEST['currentTri']= $_REQUEST['tri'];
}

if (isset($_REQUEST['currentTri'])){
	$tri= $_REQUEST['currentTri'];
}else {
	$tri="id";
}

if (isset($_REQUEST['searchId']) && $_REQUEST['searchId'] != NULL) {
	$user->setId($_REQUEST['searchId']);
} else if (isset($_REQUEST['searchUsername']) && $_REQUEST['searchUsername'] != NULL) {
	$user->setUsername($_REQUEST['searchUsername']);
} else if (isset($_REQUEST['searchEmail']) && $_REQUEST['searchEmail'] != NULL) {
	$user->setEmail($_REQUEST['searchEmail']);
}

$user->selectCount($tri);
$nbrUsers= $user->getNbrUsers();
echo("<p> nombre de user :".$nbrUsers."</p>");
$errors = array_merge($errors, $user->getErrors());

// Pagination
$limitUsers= 5;
$nbrPages= ceil($nbrUsers / $limitUsers);
$currentPage= 1;

if ($nbrPages > 0) {
	if (isset($_REQUEST['goStart'])) {

		$currentPage= intval($_REQUEST['goStart']);

	} else if (isset($_REQUEST['goEnd'])) {


		$currentPage= intval($_REQUEST['goEnd']);


	} else if (isset($_REQUEST['submit'])) {

		$currentPage= intval($_REQUEST['submit']);

	} else if (isset($_REQUEST['preview'])) {

		$currentPage= intval($_REQUEST['preview']);
		if ($currentPage == 0){
			$currentPage=1;
		}
	} else if (isset($_REQUEST['next'])) {

		$currentPage= intval($_REQUEST['next']);

		if ($currentPage > $nbrPages) {
			$currentPage = $nbrPages;
		}
	}
}
// on initialise $tri = "id" en tant que valeur par défaut (ça facilite les différents tests)

$offset = ($currentPage-1) * $limitUsers;
$user->selectAll($limitUsers,$offset,$tri);
$results = $user->getUsers();
$errors = array_merge($errors, $user->getErrors());

/*$fix = new Users($db);
$fix->setId(2);
$fix->select();
$fix->setPassword("0123");
$fix->update();
print_r($user->getErrors());*/
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Formulaire</title>
	<script type="text/javascript" src="../JS/application.js"></script>
</head>
<body>
	<div id="div_error">
		<?php 
		foreach ($errors as $error) {
			?>
			<p><?php echo $error; ?></p>
			<?php
		}
		?>
	</div>
	<p> <?php echo "$nbrUsers utilisateurs"; ?> </p>	<p> <?php echo "il y a $nbrPages pages"; ?> </p> <p> <?php echo "page actuel: $currentPage"; ?> </p>
	
	<form method="post" id="formUser" action="">
		<input type="hidden" name="currentTri" value="<?php echo $tri ?>">
		<div>
			<?php if ($tri == "id") { ?>
				<input type="text" placeholder="Recherche par id" name="searchId">

			<?php } else if ($tri == "username") { ?>

				<input type="text" placeholder="Recherche par username" name="searchUsername" value="<?php echo isset($_REQUEST['searchUsername'])? $_REQUEST['searchUsername'] : ""; ?>">

			<?php } else if ($tri == "email") { ?>

				<input type="text" placeholder="Recherche par email" name="searchEmail" value="<?php echo isset($_REQUEST['searchEmail'])? $_REQUEST['searchEmail'] : ""; ?>">

			<?php } ?>
			
			
			<button type="submit" name="search" >Rechercher</button>
			<button type="button" name="refreshSearch" onclick="refreshPage()" value="" >Actualiser</button>
		</div>
		<table>
			<tr>
				<th><button type="submit" name="tri" value="id">id</button></th>
				<th><button type="submit" name="tri" value="username">Username</button></th>
				<th><button type="submit" name="tri" value="email">Email</button></th>
				<th><button type="submit" name="tri" value="role">Role</button></th>
				<th><button type="submit" name="tri" value="is_deleted">Deleted</button></th>
			</tr>
			<?php
			foreach ($results as $obj) {
				?>
				<tr>
					<td><?php echo $obj->getId()?></td>
					<td><?php echo $obj->getUsername()?></td>
					<td><?php echo $obj->getEmail()?></td>
					<td>
						<select name="roleUser_<?php echo $obj->getId() ?>">
							<?php foreach ($tabRoles as $eachRole) { ?>
								<option value="<?php echo $eachRole->getCode() ?>" <?php echo ($obj->getRoleCode()== $eachRole->getCode())? ' selected' : '' ?>><?php echo $eachRole->getLabel() ?></option>
							<?php } ?>
						</select>
					</td>
					<td>
						<input 
						type="checkbox"
						name="deleteNew_<?php echo $obj->getId() ?>" 
						value="<?php echo $obj->getId(); ?>" 
						<?php echo ($obj->getIsDeleted())? "checked":""; ?> 
						/>
						<input 
						type="checkbox"
						name="deleteOld_<?php echo $obj->getId() ?>" 
						value="<?php echo $obj->getId(); ?>"
						<?php echo ($obj->getIsDeleted())? "checked":""; ?>
						style="display: none;"
						/>
					</td>
				</tr>


				<?php
			}
			?>

		</table>
		<button type="submit" name="goStart" value="1"> << </button>  <button type="submit" name="preview" value="<?php echo $currentPage-1 ?>"> < </button> <button type="submit" name="next" value="<?php echo $currentPage+1 ?>"> > </button>  <button type="submit" name="goEnd" value=" <?php echo $nbrPages ?>"> >> </button>
		<button type="submit" name="submit" value="<?php echo $currentPage ?>">Valider</button> <input type="reset" name="reset" value="Annuler">

	</form>
</body>
</html>