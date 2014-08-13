<?php
class connectLojasDB {
    // Vari�veis
    private $Host;
    private $Username;
    private $Pass;
    private $Database;
    // --------- //
    public $connect;
    // --------- //
    private $select_db;
    private $Status = '';
    private $StatusCode = 0;
    
    // --------- //
    // Fun��o para conex�o ao banco de dados e retorno de dados
    // @por Gabriel Roczanski Neves
    // 28/02/2013 - 15:43

    function connectLojasDB($Host, $Username, $Pass, $Database) {
        //ini_set('display_errors', 0);
        $StatusCode = 0;
        $Status = '';
        try {
            // Repasse de Variaveis $this
            $this->Host = $Host;
            $this->Username = $Username;
            $this->Pass = $Pass;
            $this->Database = $Database;

            // Try e Catch para verificar as conex�es - Erros amig�veis
            $this->connect = mysql_connect($this->Host, $this->Username, $this->Pass);
            //var_dump($this->Host." ".$this->Username." ".$this->Pass." ".$this->Database);
            mysql_select_db($this->Database, $this->connect);
            //return $this->connect;
        } catch (Exception $e) {
            $StatusCode = mysql_errno();
            $Status = $e->getMessage();
        }
    }

    // Fun��o para sa�da da conex�o
    // @por Gabriel Roczanski Neves
    // 28/02/2013 - 15:43

    public function Leave() {
        $StatusCode = 0;
        $Status = '';

        try {
            mysql_close($this->connect);
            return true;
        } catch (Exception $e) {
            $StatusCode = mysql_errno();
            $Status = $e->getMessage();
        }
    }

    // Fun��o para retorno de dados do Banco de dados
    // @por Paulo Sergio.
    // function	consulta( "Insert into user..." )	
    public function execSQL($param) {
        mysql_query($param, $this->connect);
    }

    // Fun��o para retorno de dados do Banco de dados
    // @por Gabriel Roczanski Neves
    // 28/02/2013 - 15:43
    // function	consulta( Pesquisa Query, Caso jSON true, default:false )
    public function consulta($consulta, $isJSON = false) {
        $StatusCode = 0;
        $Status = '';
        $resultSQL = array();
        $result = array();
        $nCount = 0;
        $this->consulta = $consulta;
//        echo "\n".$this->consulta;
        try {
            $resultSQL = mysql_query($this->consulta, $this->connect);
            if ($isJSON) { // Caso TRUE retornar� o valor resultSQL direto para a consulta em jSON
                return $resultSQL; // Retornando valor
            }
            while ($row = mysql_fetch_object($resultSQL)) {
                //imprimi as linhas na tela
                $result[$nCount] = $row;
                $nCount++;
            }
            return $result;
        } catch (Exception $e) {
            $StatusCode = mysql_errno();
            $Status = $e->getMessage();
        }
    }

    // Fun��o para consulta ao banco de dados usando m�todo jSON
    // @por Gabriel Roczanski Neves
    // 28/02/2013 - 15:43

    public function consulta_json($consulta) {
        $StatusCode = 0;
        $Status = '';
        try {
            // -- Vari�veis
            // -- Vari�veis jSON
            $json_result = array(); // Array do resultado jSON
            $json_select = array(); // Limpeza 
            $j = 0;  // Incrementa��o
            //$con = new ConnectDB("localhost", "root", "", "winlojas");	// Conex�o (Host,Usuario,Senha,DB)
            $getDados = $this->consulta($consulta, true); // Chamada MySQL ~ retornando com valor de $result[$nCount] 

            while ($list = mysql_fetch_array($getDados)) {
                $j = 0;
                foreach ($list as $key => $value) {
                    if (is_string($key)) {
                        $field[mysql_field_name($getDados, $j++)] = $value;
                        $fields = $field;
                    }
                }
                // -- Limpeza do Field
                $json_select[] = $fields;
                // -- Limpeza de Fields
                $fields = array();
            }
            // -- Limpeza do Select ~ passagem para o Select
            $json_result[] = $json_select;
            // -- JSON Encode  - Resultado do JSON	
            return json_encode($json_result);
        } catch (Exception $e) {
            $StatusCode = mysql_errno();
            $Status = $e->getMessage();
        }
    }

    public function getErr() {
        echo $this->Status;
    }
}
?>