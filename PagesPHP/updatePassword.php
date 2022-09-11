<?php
session_start();

require_once("../Fonctions/dbConnexion.php");
require_once("../Classes/class.users.php");

echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
$errors = array();

if (!isset($_SESSION['id'])) { 
	header("Location: login.php");
	exit();
}else { 
	$user = new Users($db);
	$user->setId($_SESSION['id']);
	$user->select();
	if ($user->getId()>0){

		if (isset($_REQUEST['submit'])) {
			if (!password_verify($_REQUEST['oldPassword'], $user->getPassword())) {
				array_push($errors, "Ancien mot de passe invalide !");
				unset($user);
			}
			if ($_REQUEST['newPassword'] !== $_REQUEST['confPassword']) {
				array_push($errors, "Les mots-de-passe doivent être identiques");
			}

			if(!count($errors)){

				$user->setPassword($_REQUEST['newPassword']);
				$user->update();
				$errors = $user->getErrors();
				
			}
		}
	} else {
		array_push($errors, "Un problème est survenu au moment de la récupération de vos informations !");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../CSS/application.css">
	<title>Update password</title>
</head>
<body>
	<h1>Modification mot-de-passe:</h1>	
	<?php if(!count($errors)) {
		echo('<p style="color: green;"> Modifié avec succés !<p>');
	}
	foreach ($errors as $error) {
		?>
		<p style="color: red;"><?php echo $error; ?></p>
		<?php
	} 
	?>
	<form method="post">
		<div>
			<label>Ancien mot-de-passe:</label><br>
			<input type="password" name="oldPassword" required><br>
			<label>Nouveau mot-de-passe:</label><br>
			<input type="password" name="newPassword" required><br>
			<label>Confirmer mot-de-passe:</label><br>
			<input type="password" name="confPassword" required><br>
			<input type="submit" name="submit" value="Modifier"><br>
			<input type="reset" name="reset" value="Annuler">
		</div>
	</form>
	<a href="login.php">Retourner vers page de login</a><br>

</body>
</html>