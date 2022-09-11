<?php 
session_start();
session_destroy();
session_start();
require_once("../Fonctions/dbConnexion.php");
require_once("../Classes/class.users.php");
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";

$errors = array();

if (isset($_REQUEST['username'])) {
	$user = new Users($db);
	$user->setUsername($_REQUEST['username']);
	$user->select();
	if ($user->getId()>0){
	//echo $user->getPassword();
		if (password_verify($_REQUEST['password'], $user->getPassword())) {
			$_SESSION['username'] = $user->getUsername();
			$_SESSION['id'] = $user->getId();
			$_SESSION['role']=$user->getRoleCode();
			$user->updateColumn("last_connexion");
			header("Location: index.php");
			exit();
		}else{
			array_push($errors, "Identifiant ou mot-de-passe incorrect ! MDP");
		}
	} else {
		array_push($errors, "Identifiant ou mot-de-passe incorrect ! ID ");
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../CSS/application.css">
	<title>Login</title>
</head>
<body>
	<h1>Connexion:</h1>
	<?php if (isset($_GET['context']) && $_GET['context'] == "réussie") { ?>
		<p style=" color : green ;">
			<?php echo("Modification réussie vous pouvez vous 
			connecter avec votre nouveau mot-de-passe !");?>
		</p>
		<?php 
	}
	?>
	<?php if (isset($_GET['context']) && $_GET['context'] == "successCreate") { ?>
		<p style="color: green;">Création de compte réussie, veuillez vous connecter !</p>;
	<?php } 
	foreach ($errors as $error) {?>		

		<p style="color: red;"><?php echo $error; ?></p>
		<?php
	} 
	?>
	<form method="post" action="login.php">
		<div>
			<label>Identifiant:</label><br>
			<input type="text" name="username" required><br>
			<label>Mot-de-passe:</label><br>
			<input type="password" name="password" required><br>
			<input type="submit" name="connexion" value="Se connecter"><br>
			<input type="reset" name="reset" value="Annuler">
		</div>
		<a href="signup.php">Créer un compte</a><br>
		<a href="forgotPassword.php">Mot de passe oublié ?</a><br>
	</form>
	<div>

	</div>
	
</body>
</html>