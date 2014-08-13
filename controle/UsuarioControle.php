<?php
include '../dao/UserDao.php';
include_once '../entidade/class.user.php';
include_once '../entidade/class.permissao.php';

$funcao = $_REQUEST["action"];
if (session_id() == "") {
    session_start();
}
/* ----------------------------------------------------------------------------- */
if (function_exists($funcao)) {
    call_user_func($funcao);
}
/* ---------------------------------------------------------------------------- */
function salvar() {
    echo $dao->salvar($user,$permissao);
}
/* ---------------------------------------------------------------------------- */
function alterar() {
    echo $dao->alterar($user,$permissao);
}
/* ---------------------------------------------------------------------------- */
function deletar() {
    $dao = new UserDao();
    $id = $_POST["ID"];
    if ($id == $_SESSION['idUser']) {
        echo 'user';
    }else{
        echo $dao->deletar($id);
    }
}
/* ---------------------------------------------------------------------------- */
function pesq() {
    $dao = new UserDao();
    $pagina = $_POST["Pagina"];
    echo $dao->pesqAll($pagina);
}
/* ---------------------------------------------------------------------------- */
function pesqId() {
    $dao = new UserDao();
    $id = $_POST["ID"];
    echo $dao->pesqId($id);
}
/* ---------------------------------------------------------------------------- */
function preAltera() {
    $retorno = array();
    $retorno ["login"] = $_SESSION["login"];
    $retorno ["senha"] = $_SESSION["senha"];
    $retorno ["nome"] = $_SESSION["nome"];
    $retorno ["id"] = $_SESSION["id"];
    $retorno ["status"] = $_SESSION["status"];

    echo json_encode($retorno, JSON_HEX_QUOT);

    unset($_SESSION['id']);
    unset($_SESSION["status"]);
    unset($_SESSION["login"]);
    unset($_SESSION["senha"]);
    unset($_SESSION["nome"]);
}
/* ---------------------------------------------------------------------------- */
function login() {
    $user = new User();
    $dao = new UserDao();

    $user->login = $_POST["Login"];
    $user->senha = $_POST["Senha"];
    echo $dao->login($user);
}
