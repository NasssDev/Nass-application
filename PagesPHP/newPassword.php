<?php

require_once("../Fonctions/dbConnexion.php");
require_once("../Classes/class.users.php");

echo "<pre>";
print_r($_REQUEST);
echo "<pre>";
$errors = array();

$id=0;
if (isset($_GET['key'])) {
	$user = new Users($db);
	$user->setEmail(base64_decode($_GET['key']));
	$user->select();
	if ($user->getId()<1){
		
		header("Location: forgotPassword.php?erreur=user_not_exist");
		exit();
	} else {
		$id=$user->getId();
	}
} elseif (!isset($_GET['key']) && !isset($_REQUEST['submit'])){
	header("Location: forgotPassword.php?erreur=url_not_exist");
	exit();
}

if (isset($_REQUEST['newPassword']) && isset($_REQUEST['confPassword']) && isset($_REQUEST['id'])) {

	$id=$_REQUEST['id'];
	// bien utiliser !== et non != pour tester l'égalité parfaite 
	if ($_REQUEST['newPassword'] !== $_REQUEST['confPassword']) {
		array_push($errors, "Les mots-de-passes doivent être identique");
	}

	if(!count($errors)){

		$user = new Users($db);
		$user->setId($_REQUEST['id']);
		$user->select();
		if($user->getId()>0) {

			$user->setPassword($_REQUEST['newPassword']);
			$user->update();
			$errors = $user->getErrors();

			if(!count($errors)){
				header("Location: login.php?context=réussie");
				exit();
			} else {
				array_push($errors,"Erreur suspecte !!!");
			}
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../CSS/application.css">
	<title>New password</title>
</head>
<body>
	<h1>Créer votre nouveau mot de passe:</h1>
	<?php 
	foreach ($errors as $error) {
		?>
		<p style="color: red;"><?php echo $error; ?></p>
		<?php
	} 
	?>
	<form method="post" action="newPassword.php">
		<div>
			<label>Nouveau mot-de-passe:</label><br>
			<input type="password" name="newPassword" required><br>
			<label>Confirmer mot-de-passe:</label><br>
			<input type="password" name="confPassword" required><br>
			<input type="hidden" name="id" value="<?php echo ($id) ?>">
			<input type="submit" name="submit" value="Modifier">
			<input type="reset" name="reset" value="Annuler">
		</div>
	</form>
</body>
</html>