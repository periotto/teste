<?php
/*
 * Paulo Sergio D. Junho de 2013 Este script � respons�vel por logar o user no
 * winlojasweb e popular uma $_SESSION com os dados do user e tambem montar o
 * "ambiente" (setar as variaveis corretas) para cada tipo de usu�rio
 */
function get_start_login($cUser, $cPass) {
	require_once("conncfg.php");
	require_once("class.connectLojasDB.php"); //Toda conex�o com o BD principal vem atrav�s desta configura��o
	require_once("datetools.php");
		
	$isValidUserLogin = false;
	
	
	$OBJUserDB = new connectLojasDB($WLHostCFG, $WLUserCFG, $WLPassCFG, $WLDBCFG);
	//Tratamento b�sico contra SQL Injection
	//$cUser = mysql_real_escape_string($cUser);
	//$cPass = mysql_real_escape_string($cPass);
        
    //Pesquisa user e senha enviados pra login
	$rs = $OBJUserDB->consulta("SELECT * FROM user WHERE username ='".$cUser."' AND password ='".$cPass."'");
	

	//Se encontrou algum registro ir� iniciar uma sess�o com os dados do user
	foreach ($rs as $getUser) {
		//Abre uma sess�o
		if (! isset ( $_SESSION )) {
			// session_cache_expire(10);
			session_start ();
			$_SESSION = array ();
		}
		
		/* Dados do Usu�rio */
		$_SESSION ['USER'] 		= $getUser->username;
		$_SESSION ['USERID'] 	= $getUser->id;
		$_SESSION ['USERNAME'] 	= $getUser->name;
		$_SESSION ['lLogin'] 	=  mysql_datetime_para_humano($getUser->last_login);
		$_SESSION ['NIVEL'] 	= 1111; // -> visualizar, Criar, Alterar, Excluir
		$_SESSION ['USERTIPO'] 	= 'Administrador';
		$_SESSION ['HOME'] 		= '/winlojas/var/user/' . $_SESSION ['USER'];

		$OBJUserDB->execSQL("UPDATE user SET last_login= now() where id=".(string)$getUser->id);
		$isValidUserLogin = true;	

	//echo "<script>alert('".$_SESSION ['NIVEL']."');</script>";
	}

	/* Dados do Ambiente */
	
	/* Dados do Usu�rio 
	$_SESSION ['USER'] 		= 'suporte';
	$_SESSION ['USERID'] 	= 1;
	$_SESSION ['USERNAME'] 	= 'Paulo Sergio';
	$_SESSION ['lLogin'] 	= date ( "d-m-Y H:i" );
	$_SESSION ['NIVEL'] 	= 1111; // -> visualizar, Criar, Alterar, Excluir
	$_SESSION ['USERTIPO'] 	= 'Administrador';*/
	
	$OBJUserDB->Leave();
	
	return $isValidUserLogin;
}
?>