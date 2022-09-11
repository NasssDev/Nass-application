<?php
session_start();

require_once("../Fonctions/dbConnexion.php");
require_once("../Classes/class.users.php");

echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
$errors = array();


if (isset($_REQUEST['username'])) {
	if ($_REQUEST['password'] !== $_REQUEST['confPassword']){
		array_push($errors, "Les mots-de-passes doivent être identique");
	}
	if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) == false){
		array_push($errors, "Format d'e-mail non valide !");
	}
	if(!count($errors)){
		$user = new Users($db);
		$user->setUsername($_REQUEST['username']);
		$user->setEmail($_REQUEST['email']);
		$user->setPassword($_REQUEST['password']);
		$user->insert();
		$errors = $user->getErrors();
		if($user->getId()>0) {
			header("Location: login.php?context=successCreate");
			exit();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sign Up</title>
	<link rel="stylesheet" type="text/css" href="../CSS/application.css">
</head>
<body>
	<h1>Créez votre compte:</h1>
	<?php 
	foreach ($errors as $error) {
		?>
		<p style="color: red;"><?php echo $error; ?></p>
		<?php
	} 
	?>
	<form method="post">
		<div>
			<label>Identifiant:</label><br>
			<input type="text" name="username" required><br>
			<label>E-mail:</label><br>
			<input type="text" name="email" class="<?php if(isset($_REQUEST['email'])) echo(filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)?'valid_class':'invalid_class');?>" required><br>
			<label>Mot-de-passe:</label><br>
			<input type="password" name="password" required><br>
			<label>Confirmer mot-de-passe:</label><br>
			<input type="password" name="confPassword" required><br>
			<input type="submit" name="submit" value="S'enregistrer"><br>
			<input type="reset" name="reset" value="Annuler">
		</div>
	</form>
	<a href="login.php">Déjà un compte ?</a><br>
	
</body>
</html>