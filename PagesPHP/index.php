<?php 
session_start();

if (!isset($_SESSION['username'])) {
	header("Location:login.php");
	exit();
}

echo "<pre>";
print_r($_REQUEST);
echo "</pre><br>";

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../CSS/application.css">
	<title>Page d'accueil</title>
</head>
<body>
	<div class="logoKali">
		<img style="height: 10vh; width: 90%" src="../Ressources/logo-kali.png">
	</div>
	<div class="navButton">
		<a  href="updateAccount.php"><button>Modifier compte</button></a>
		<a  href="updatePassword.php"><button>Modifier mot de passe</button></a>
		<?php if(isset($_SESSION['role']) && $_SESSION['role'] == "ADMIN") { ?> 
			<a  href="listUserAdmin.php"><button>Liste des utilisateurs</button></a>
		<?php } ?>
		<a  href="login.php"><button>Déconnexion</button></a><br>
	</div>
	<h1>Bienvenue à toi <?php echo $_SESSION['username']; ?> ! Bravo pour votre nouvelle recrue ;-)</h1><br>
	<img style="height: 80vh; width: 90%;" src="../Ressources/schemaKali.svg"><br>
	<a href="login.php">Retourner vers page de login</a><br>
	<a href="signup.php">Retourner vers page de signup</a><br>
</body>
</html>