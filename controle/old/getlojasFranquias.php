<?php
require_once 'controls/shared/weblojas.php';

    $idFranquia = isset ( $_POST["idFranquia"] ) 	? $_POST["idFranquia"] 	: 0; //$_POST["idFranquia"];
    $idLoja 	= isset ( $_POST["idLoja"] ) 		? $_POST["idLoja"] 		: 0; //$_POST["idLoja"];
    $dataINI 	= isset ( $_POST["dataINI"] ) 		? $_POST["dataINI"] 	: date("m.d.y"); //$_POST["dataINI"];
    $dataFIN 	= isset ( $_POST["dataFIN"] ) 		? $_POST["dataFIN"] 	: date("m.d.y"); //$_POST["dataFIN"];

  	$myWebLoja = new weblojas($idLoja,$idFranquia,$dataINI, $dataFIN);
	echo $myWebLoja->getLojasJSON();
?>