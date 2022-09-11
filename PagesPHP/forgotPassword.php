<?php

require_once("../Fonctions/dbConnexion.php");
require_once("../Classes/class.users.php");
require_once("../Fonctions/PHPMailer.php");

echo "<pre>";
print_r($_SERVER);
echo "</pre>";
$errors = array();

/* on renvoie sur cette page en cas d'erreur sur la page "newPassword.php" (page accessible par un lien envoyé par mail 
permetant de recréer un mot de passe oublié)*/
if (isset($_REQUEST['erreur'])) {

	switch ($_REQUEST['erreur']) {
		case 'user_not_exist':
		array_push($errors, "Le mail est inexistant veuillez entrer un mail existant !");
		break;
		
		case 'url_not_exist':
		array_push($errors, "Le lien n'est pas valide veuillez réessayer !");
		break;
		
		default:

		break;
	}
}


if (isset($_REQUEST['email'])) {
	
	$_SESSION['email'] = $_REQUEST['email'];
	if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) == false){
		array_push($errors, "Format d'e-mail non valide !");
	}
	if(!count($errors)){

		$user = new Users($db);
		$user->setEmail($_REQUEST['email']);
		$user->select();
		$errors = $user->getErrors();
		if($user->getId()>0) {
			$iniFileName = '../../MonApplication.ini';
			if (file_exists($iniFileName)) {

				$ini_array = parse_ini_file($iniFileName,true);
				$from = $ini_array['MAIL']['from'];

				$tabAdress = array(); 
				array_push($tabAdress, ["adress" => $user->getEmail(), "name" => $user->getUserName()]);

				$reply = $ini_array['MAIL']['reply'];
				$replyName = $ini_array['MAIL']['reply_name'];

				$tabCC = array();
				
				$tabAttachments = array();
				
				$isHTML = true;

				$subject = 'Renouvellement mot de passe '.date('d.m.Y H:i:s');
				$body    = '<h3>Vous avez fait une demande de mot de passe, voici le lien qui vous permettra de le mettre à jour ;) !</h3><br><p><a href="http://'.$_SERVER["SERVER_NAME"].str_replace("forgotPassword", "newPassword", $_SERVER["REQUEST_URI"]).'?key='.base64_encode($_REQUEST['email']).'"> Cliquez ici !</a></p>';
				$altBody = ' !! ';

				sendMail($from, $tabAdress, $reply, $replyName, $isHTML, $subject, $body, $altBody, $tabCC, $tabAttachments);
				//sendMail($from, $tabAdress, $reply, $replyName, $isHTML, $subject, $body);
				header("Location: login.php");
				exit();	
			}
		} else {
			array_push($errors, "Utilisateur inexistant");
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
	<title>Forgot Password</title>
</head>
<h1>Récupération de mot de passe:</h1>
<?php 
foreach ($errors as $error) {
	?>
	<p ><?php echo $error; ?></p>
	<?php
} 
?>
<body>
	<form method = "post">
		<div>
			<label>E-mail:</label><br>
			<input type="text" name="email" required><br>
			<input type="submit" name="submit" value="S'enregistrer">
		</div>
	</form>
	
</body>
</html>