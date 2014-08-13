<?php
include '../dao/PermissaoDao.php';
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
    
    echo $dao->salvar($permissao);
}
/* ---------------------------------------------------------------------------- */
function alterar() {
    
    echo $dao->alterar($permissao);
}
/* ---------------------------------------------------------------------------- */
function deletar() {
        echo $dao->deletar($id);
}
/* ---------------------------------------------------------------------------- */
function pesq() {
    $dao = new PermissaoDao();
    echo $dao->pesq();
}
/* ---------------------------------------------------------------------------- */
function pesqId() {
    $dao = new UserDao();
    $id = $_POST["ID"];
    echo $dao->pesqId($id);
}
/* ---------------------------------------------------------------------------- */
function preAltera() {
    
}