<?php
class User {
    Public $id;
    Public $nome;
    Public $email;
    Public $login;
    Public $senha;
    Public $ultimo_login;
    Public $data_cad;
    Public $user_cad;

    public function getId() {
        return $this->id;
    }
    public function getNome() {
        return $this->nome;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getLogin() {
        return $this->login;
    }
    public function getSenha() {
        return $this->senha;
    }
    public function getUltimo_login() {
        return $this->ultimo_login;
    }
    public function getData_cad() {
        return $this->data_cad;
    }
    public function getUser_cad() {
        return $this->user_cad;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setLogin($login) {
        $this->login = $login;
    }
    public function setSenha($senha) {
        $this->senha = $senha;
    }
    public function setUltimo_login($ultimo_login) {
        $this->ultimo_login = $ultimo_login;
    }
    public function setData_cad($data_cad) {
        $this->data_cad = $data_cad;
    }
    public function setUser_cad($user_cad) {
        $this->user_cad = $user_cad;
    }
}