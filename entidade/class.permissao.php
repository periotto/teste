<?php
class permissao {
    Public $id;
    Public $id_user;
    Public $cad_lojas;
    Public $cad_user;
    Public $cad_franquia;
    Public $cadProduto;
    public $ver_vendas;

    function permissao() {

    }
    
    public function getId() {
        return $this->id;
    }
    public function getId_user() {
        return $this->id_user;
    }
    public function getCad_lojas() {
        return $this->cad_lojas;
    }
    public function getCad_user() {
        return $this->cad_user;
    }
    public function getCad_franquia() {
        return $this->cad_franquia;
    }
    public function getCadProduto() {
        return $this->cadProduto;
    }
    public function getVer_vendas() {
        return $this->ver_vendas;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function setId_user($id_user) {
        $this->id_user = $id_user;
    }
    public function setCad_lojas($cad_lojas) {
        $this->cad_lojas = $cad_lojas;
    }
    public function setCad_user($cad_user) {
        $this->cad_user = $cad_user;
    }
    public function setCad_franquia($cad_franquia) {
        $this->cad_franquia = $cad_franquia;
    }
    public function setCadProduto($cadProduto) {
        $this->cadProduto = $cadProduto;
    }
    public function setVer_vendas($ver_vendas) {
        $this->ver_vendas = $ver_vendas;
    }
}