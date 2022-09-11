<?php 
$iniFileName = '../../MonApplication.ini';
if (file_exists($iniFileName)) {
/*echo "mon fichier existe <br>";*/

	$ini_array = parse_ini_file($iniFileName,true);
	
	/*print_r($ini_array);*/
	$host = $ini_array['DATABASE']['host'];
	$port = $ini_array['DATABASE']['port'];
	$dbname = $ini_array['DATABASE']['db_name'];
	$username = $ini_array['DATABASE']['user_name'];
	$password = $ini_array['DATABASE']['password'];
	$dbtype = $ini_array['DATABASE']['db_type'];
	$charset = '';
	if (isset($ini_array['DATABASE']['charset'])) {
		$charset = $ini_array['DATABASE']['charset'];
	}

	try{
		$strConnection = "$dbtype:host=$host;port=$port;dbname=$dbname;";
		if (strlen($charset)>0) {
			$strConnection.="charset=$charset";
		}
		$db = new PDO($strConnection, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		if($db){
			/*echo "Connecté à $dbname avec succès!<br>";*/
		}
	} catch (PDOException $e){
		die('Erreur : ' . $e->getMessage());
	}

} else {
	die("Non existant");
}
?>