<?php
//ela herdará os métodos e atributos do PDO através da palavra-chave extends
class Conexao extends PDO {
    //criando a conexão com o banco de dados no localhost(127.0.0.1) e falando o nome do banco
    private $dsn = "mysql:host=127.0.0.1:3307;dbname=weblojas";
    public $handle = null;
    
    public $user = "root";
    public $password = "usbw";
//    public $user = "weblojas";
//    public $password = "BrasBanco2000";


    function __construct() {
        try {
            //aqui ela retornará o PDO em si, veja que usamos parent::_construct()
            if ($this->handle == null) {
                $dbh = parent::__construct($this->dsn, $this->user, $this->password);
                $this->handle = $dbh;
                return $this->handle;
            }
        } catch (PDOException $e) {
            echo "Conexão falhou. Erro: " . $e->getMessage();
            return false;
        }
    }
    //aqui criamos um objeto de fechamento da conexão
    function __destruct() {
        $this->handle = NULL;
    }
}