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
}else{ 
	$user = new Users($db);
	$user->setId($_SESSION['id']);
	$user->select();
	if ($user->getId()>0){
		if (isset($_REQUEST['submit'])) {
			$username = $_REQUEST['username'];
			$email = $_REQUEST['email'];
			if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) == false){
				array_push($errors, "Format d'e-mail non valide !");
			}
			if(!count($errors)) {
				
				$user->setUsername($_REQUEST['username']);
				$user->setEmail($_REQUEST['email']);
				$user->setPassword();
				$user->update();
				$errors = $user->getErrors();
				if (!count($errors)) {
					$_SESSION['username'] = $user->getUsername();
					echo("<p style='color: green;'>Modifié avec succés !</p>");
				}
			}
		} else {
			$email = $user->getEmail();
			$username = $user->getUsername();
		}
	} else {
		array_push($errors, "Aucun enregistrement n'a été trouvé !");
		$errors = $user->getErrors();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../CSS/application.css">
	<title>Update account</title>
</head>
<body>
	<h1>Modification du compte:</h1>
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
			<input type="text" name="username" value="<?php echo($username)?>" required>
			<br>
			<label>E-mail:</label>
			<br>
			<input type="text" name="email" value="<?php echo($email)?>" class="<?php if (isset($_REQUEST['email'])) echo(filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)?'valid_class':'invalid_class');?>" required>
			<br>
			<input type="submit" name="submit" value="Modifier"><br>
			<input type="reset" name="reset" value="Annuler">
		</div>
	</form>
	<a href="login.php">Retourner vers page de login</a><br>
	<a href="index.php">Retourner vers page d'accueil</a><br>
	
</body>
</html>