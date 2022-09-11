<?php
/*require_once('..\Fonctions\PHPMailer.php');

session_start();
$_SESSION['APPLICATION_NAME'] = "MonApplication";
print_r($_SESSION);
$log = new Log();
$log->email("Test de message d'erreur");*/

class Log {

	private $user_error_dir;
	private $general_error_dir;

	public function __construct() 
	{
		if (!empty($_SESSION['APPLICATION_NAME'])) {
			$this->user_error_dir = $_SERVER["DOCUMENT_ROOT"]."/logError/".strtolower($_SESSION['APPLICATION_NAME'])."_user_errors.log";
			$this->general_error_dir = $_SERVER["DOCUMENT_ROOT"]."/logError/".strtolower($_SESSION['APPLICATION_NAME'])."_general_errors.log";
		}
		else {
			$this->user_error_dir = $_SERVER["DOCUMENT_ROOT"]."/logError/user_errors.log";
			$this->general_error_dir = $_SERVER["DOCUMENT_ROOT"]."/logError/general_errors.log";
		}
	}

	public function general($msg)
	{
		date_default_timezone_set('Europe/Paris');
		$date = date('d.m.Y H:i:s');
		$log = "[Date:  ".$date."] ".$msg.PHP_EOL;
		error_log($log, 3, $this->general_error_dir);
	}

	public function user($msg)
	{
		date_default_timezone_set('Europe/Paris');
		$date = date('d.m.Y H:i:s');
		$log = "[Date:  ".$date."] ".$msg.PHP_EOL;
		error_log($log, 3, $this->user_error_dir);
	}

	public function email($msg)
	{
		$iniFileName = $_SERVER["DOCUMENT_ROOT"].'/monApplication.ini';

		if (file_exists($iniFileName)) {
		    $ini_array = parse_ini_file($iniFileName, true);
		    //print_r($ini_array);
		    
		    $from = $ini_array['MAIL']['from'];

		    $tabAdress = array(); 
		    array_push($tabAdress, ["adress" => $ini_array['MAIL']['adress'], "name" => $ini_array['MAIL']['adress_name']]);

		    $reply = $ini_array['MAIL']['reply'];
    		$replyName = $ini_array['MAIL']['reply_name'];

		    $isHTML = true;
    		
    		date_default_timezone_set('Europe/Paris'); 
	    	$date = date('d.m.Y H:i:s');
		    $subject = 'Erreur ';
		    if (!empty($_SESSION['APPLICATION_NAME'])) $subject .= ' '.$_SESSION['APPLICATION_NAME'];
		    $body    = "[Date:  ".$date."] ".$msg;

		    //sendMail($from, $tabAdress, $reply, $replyName, $isHTML, $subject, $body, $altBody, $tabCC, $tabAttachments);
		    sendMail($from, $tabAdress, $reply, $replyName, $isHTML, $subject, $body);
		}
		else {
		    date_default_timezone_set('Europe/Paris'); 
		    $date = date('d.m.Y H:i:s');
		    $log = "[Date:  ".$date."] Le fichier d'initialisation n'existe pas : ". $iniFileName.". Message non envoyé : ".$msg.PHP_EOL;
		    error_log($log, 3, $this->general_error_dir);			
		}
	}
}

?>