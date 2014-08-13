<?php
include_once('Conexao.php');

if (session_id() == "") {
    session_start();
}
$retorno = array();
if (!isset($_SESSION["id_user"])) {
    $retorno["sessao"] = 'false';
} else {
    $retorno["sessao"] = "true";
    $retorno["nome"] = $_SESSION["nome"];
    $retorno["ultimo_login"] = $_SESSION["ultimo_login"];
    $retorno["cad_franquia"] = $_SESSION["cad_franquia"];
    $retorno["cad_produto"] = $_SESSION["cad_produto"];
    $retorno["ver_vendas"] = $_SESSION["ver_vendas"];
    $retorno["cad_lojas"] = $_SESSION["cad_lojas"];
    $retorno["cad_user"] = $_SESSION["cad_user"];
}
echo json_encode($retorno, JSON_HEX_QUOT);