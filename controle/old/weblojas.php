<?php
/* Paulo Sergio D. Brasystem 2013 */
include("class.connectLojasDB.php");
include("Cep.php");

class weblojas {
    private $field;
    private $json_result;
    private $JSON;
    private $fields;
    private $json_select;
    private $result;
    private $ncont;
    private $dataINI;
    private $dataFIN;
    private $idLoja;
    private $idFranquia;
    private $dataINI_inverse;
    private $dataFIN_inverse;
    private $connectLojas;
/*----------------------------------------------------------------------------*/
    function weblojas($idLojaFilter, $idFranquiaFilter, $dataINICIAL, $dataFINAL) {
        require_once("conncfg.php");
        //define o time zone
        date_default_timezone_set('GMT');
        
        $this->dataINI = $dataINICIAL;
        $this->dataFIN = $dataFINAL;
        $this->field = "";
        $this->json_result = array();
        $this->JSON = array();
        $this->fields = array();
        $this->json_select = array();
        $this->result = array();
        $this->ncont = 0;
        $this->idLoja = $idLojaFilter;
        $this->idFranquia = $idFranquiaFilter;
        $this->dataINI_inverse = '';
        $this->dataFIN_inverse = '';
        $this->connectLojas = "";
        $this->itensjSON = array();

        //Essas configurações são padrão para acesso a base de dados do WEBLOJAS
        //Deverá ser feito uma leitura de um arquivo externo com essas configs, futuramente.        
        $this->WLServerHost = $WLHostCFG; //Essas configurações estão dentro do arquivo "conncfg.php"
        $this->WLServerUser = $WLUserCFG;
        $this->WLServerPass = $WLPassCFG;
        $this->WLServerDB = $WLDBCFG;
    }
/*-----------------------Função para retirar acentos de textos----------------*/
    function retira_acentos($texto) {
        $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
            , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
        $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
            , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
        return str_replace($array1, $array2, $texto);
    }

/*----------------------------------------------------------------------------*/
    public function getMarkers(){
        try {
            $getDados = $this->getConWebLojas();
            // Conexão feita com o banco de dados exterior
            // Retornando valores para manipulação
            $k = 0;
            foreach ($getDados as $tmpLoja) {
                $this->connectLojas = new connectLojasDB($tmpLoja->host, $tmpLoja->user, $tmpLoja->password,
                        $tmpLoja->banco_de_dados);
                
                $getInfos = $this->connectLojas->consulta("SELECT * FROM loja LIMIT 1");
                //print_r($getInfos);

                foreach ($getInfos as $tmpEndereco) {
                    $retornojSON[$k]["DESCRICAO"] = $tmpLoja->descricao;
                    $retornojSON[$k]["FRANQUIA"] = $tmpLoja->nome;
//                    echo $tmpLoja->nome;
                    if ($tmpEndereco->ENDERECO == null) {
                        if ($tmpEndereco->CEP != NULL) {
                            $resultado_busca = new busca_cep($tmpEndereco->CEP);
                            $retornojSON[$k]["ENDERECO"] = $resultado_busca->endereco_Completo;
                        }
                    } else {
                        $retornojSON[$k]["ENDERECO"] = $tmpEndereco->ENDERECO . ', ' . $tmpEndereco->NUMERO 
                                . ' - ' . $tmpEndereco->CIDADE . ' - ' . $tmpEndereco->UF; // ENDEREÇO COMPLETO                    
                    }

                    $retornojSON[$k]["ENDERECO"] = $this->retira_acentos($retornojSON[$k]["ENDERECO"]);

                    // Dados para o retorno ao jSON <AJAX
                    //echo $retornojSON[$k]["ENDERECO"];
                    $retornojSON[$k]["VALOR"] = $this->calculaValorVendas(); // VALOR
                    $retornojSON[$k]["LOJA"] = $tmpLoja->id; // ID LOJA
                    $retornojSON[$k]["IDFRANQUIA"] = $tmpLoja->id_franquia; // ID FRANQUIA
                    $k++;
                }
            }
            // Saindo da conexão $connectLojas
            $this->connectLojas->Leave();
            //var_dump($retornojSON);
            return json_encode($retornojSON, JSON_HEX_QUOT);
        } catch (Exception $e) {
            echo $e;
        }
    }
/*----------------------------------------------------------------------------*/
    private function calculaValorVendas() {
        $getInfos = "";
        $vendasSQL = "";

        // Invertendo de YYYY/MM/DD para DD/MM/YYYY e vice-versa
        $dataINI_inverse = implode(preg_match("~\/~", $this->dataINI) == 0 ? "/" : "-", 
                array_reverse(explode(preg_match("~\/~", $this->dataINI) == 0 ? "-" : "/", $this->dataINI)));
        $dataFIN_inverse = implode(preg_match("~\/~", $this->dataFIN) == 0 ? "/" : "-", 
                array_reverse(explode(preg_match("~\/~", $this->dataFIN) == 0 ? "-" : "/", $this->dataFIN)));
        $vendasSQL = "SELECT CODIGOVENDA, DATA, TOTAL, CANCELADA, SUM(TOTAL) as totVAR FROM venda WHERE data >= '" 
                . $dataINI_inverse . "' AND data <= '" . $dataFIN_inverse . "' AND CANCELADA IS NULL";
//        echo $vendasSQL;
        $getInfos = $this->connectLojas->consulta($vendasSQL);

        // Retornando R$+VALOR ao gMaps
        return (string) number_format($getInfos[0]->totVAR, 2, ',', '.');
    }
/*----------------------------------------------------------------------------*/
    public function getVendaItens($codVenda) {
        $getDados = $this->getConWebLojas();
        // Conexão feita com o banco de dados exterior
        // Retornando valores para manipulação
        $k = 0;
        foreach ($getDados as $tmpLoja) {
            // Variaveis
            $getVendas = "";
            $vendasSQL = "";

            $dataINI_inverse = implode(preg_match("~\/~", $this->dataINI) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", 
                    $this->dataINI) == 0 ? "-" : "/", $this->dataINI)));
            
            $dataFIN_inverse = implode(preg_match("~\/~", $this->dataFIN) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", 
                    $this->dataFIN) == 0 ? "-" : "/", $this->dataFIN)));

            $this->connectLojas = new connectLojasDB($tmpLoja->host, $tmpLoja->user, $tmpLoja->password, $tmpLoja->banco_de_dados);
            $vendaSQL = "SELECT CODIGOLOJA, CODIGOVENDA, DATA, TOTAL, CANCELADA FROM venda where data >= '"
                    .$dataINI_inverse. "' AND data <= '" . $dataFIN_inverse . "' AND CANCELADA IS NULL";

            if (strlen($codVenda) > 0) {
                $vendaSQL = $vendaSQL . " AND CODIGOVENDA = " . $codVenda;
            }
            //echo $vendaSQL;
            $getVendas = $this->connectLojas->consulta($vendaSQL);

            foreach ($getVendas as $getVenda2) {

                // Variaveis
                $vendaItensSQL = "";
                $getVendaItens = "";

                //------
                // Selecionando Tabelas pelo codigo de venda, e descrição
                $vendaItensSQL = "SELECT t1.*, produto.DESCRICAO FROM venda_itens as t1  INNER JOIN produto ON "
                    ."(t1.CODIGOPRODUTO = produto.CODIGOPRODUTO) WHERE CODIGOVENDA = '" . $getVenda2->CODIGOVENDA . "'";
            //echo $vendaItensSQL;
                $getVendaItens = $this->connectLojas->consulta($vendaItensSQL);
                foreach ($getVendaItens as $getVendaItem) {
                    // Retornando valores em jSON Encode
                    $this->itensjSON[$k]["produto"] = $getVendaItem->CODIGOPRODUTO;
                    $this->itensjSON[$k]["descricao"] = $getVendaItem->DESCRICAO;
                    $this->itensjSON[$k]["valor"] = $getVendaItem->VALOR;
                    $this->itensjSON[$k]["quantidade"] = $getVendaItem->QUANTIDADE;
                    //$this->itensjSON[$k]["total"]      = (string)($getVendaItem->VALOR*$getVendaItem->QUANTIDADE);
                    // Transformando valores em BRL
                    $this->itensjSON[$k]["total"] = number_format(($getVendaItem->VALOR * $getVendaItem->QUANTIDADE), 2, ',', '.');
                    $this->itensjSON[$k]["valor"] = number_format($this->itensjSON[$k]["valor"], 2, ',', '.');
                    $k++;
                    //echo $this->itensjSON;
                }
            }
        }
        // Saindo da conexão $connectLoja
        $this->connectLojas->Leave();

        echo json_encode($this->itensjSON, JSON_HEX_QUOT);
    }
/*----------------------------------------------------------------------------*/
    public function getVendas($VendaFiltro = "") {
        // Conexão feita com o banco de dados exterior
        // Retornando valores para manipulação
        $getDados = $this->getConWebLojas();
        $k = 0;
        foreach ($getDados as $tmpLoja) {
            // Variaveis
            $getVendas = "";
            $vendasSQL = "";

            $dataINI_inverse = implode(preg_match("~\/~", $this->dataINI) == 0 ? "/" : "-", 
                    array_reverse(explode(preg_match("~\/~", $this->dataINI) == 0 ? "-" : "/", $this->dataINI)));
            $dataFIN_inverse = implode(preg_match("~\/~", $this->dataFIN) == 0 ? "/" : "-", 
                    array_reverse(explode(preg_match("~\/~", $this->dataFIN) == 0 ? "-" : "/", $this->dataFIN)));

            $this->connectLojas = new connectLojasDB($tmpLoja->host, $tmpLoja->user, $tmpLoja->password,
                    $tmpLoja->banco_de_dados);
            $vendaSQL = "SELECT CODIGOLOJA, CODIGOVENDA, DATA, TOTAL, CANCELADA, NOME_TIPO FROM venda where"
                  ." data >= '" . $dataINI_inverse . "' and data <= '" . $dataFIN_inverse . "' and CANCELADA  is null";

            if (strlen($VendaFiltro) > 0) {
                $vendaSQL = $vendaSQL . " and CODIGOVENDA = " . $VendaFiltro;
            }
            //echo $vendaSQL;
            $getVendas = $this->connectLojas->consulta($vendaSQL);
            //var_dump($getVendas);
            foreach ($getVendas as $getVenda) {
                // Retornando valores em jSON Encode
                $this->itensjSON[$k]["codvenda"] = $getVenda->CODIGOVENDA;
                $this->itensjSON[$k]["data"] = date("d/m/Y", strtotime($getVenda->DATA));
                $this->itensjSON[$k]["pgto"] = $getVenda->NOME_TIPO;
                $this->itensjSON[$k]["valor"] = number_format($getVenda->TOTAL, 2, ',', '.');
                $k++;
            }
        }
        // Saindo da conexão $connectLoja
//        $this->connectLojas->Leave();
        echo json_encode($this->itensjSON, JSON_HEX_QUOT);
    }
/*----------------------------------------------------------------------------*/
    private function getConWebLojas(){
        // Conectando ao banco de dados interior
        $conWeblojas = new connectLojasDB($this->WLServerHost, $this->WLServerUser, $this->WLServerPass,
               $this->WLServerDB);
        //new connectLojasDB("localhost", "root", "", "weblojas");  // Conexão (Host,Usuario,Senha,DB)
        //$tmpSQLConsultaLojas = "select lojas.* FROM lojas order by descricao";
        $tmpSQLConsultaLojas = "SELECT lojas.*, franquias.nome FROM lojas JOIN "
                . "franquias ON lojas.id_franquia = franquias.id ";
        // Filtrando ID da Franquia
        if ($this->idFranquia > 0) {
            $tmpSQLConsultaLojas .= "WHERE franquias.id =" . (string) $this->idFranquia;
        }
        // Filtrando ID da Loja
        if ($this->idLoja > 0) {
            // Filtrando ID da Franquia junto com o da Loja
            if ($this->idFranquia > 0) {
                $tmpSQLConsultaLojas .= " AND lojas.id =" . (string) $this->idLoja;
            } else {
                $tmpSQLConsultaLojas .= " WHERE lojas.id =" . (string) $this->idLoja;
            }
        }
        $tmpSQLConsultaLojas .= " ORDER BY lojas.descricao";
        //echo $tmpSQLConsultaLojas;
        $getDados = $conWeblojas->consulta($tmpSQLConsultaLojas); // Chamada MySQL    	
        // Saindo da conexão $con
        $conWeblojas->Leave();
        return $getDados;
    }
/*----------------------------------------------------------------------------*/
     public function getTiposPGTO() {
        $getDados = $this->getConWebLojas();
        // Conexão feita com o banco de dados exterior
        // Retornando valores para manipulação
        $auxTot = 0;
        $k = 0;
        foreach ($getDados as $tmpLoja) {
            // Variaveis
            $getTipos = "";
            $vendasSQL = "";

            $dataINI_inverse = implode(preg_match("~\/~", $this->dataINI) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $this->dataINI) == 0 ? "-" : "/", $this->dataINI)));
            $dataFIN_inverse = implode(preg_match("~\/~", $this->dataFIN) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $this->dataFIN) == 0 ? "-" : "/", $this->dataFIN)));

            $this->connectLojas = new connectLojasDB($tmpLoja->host, $tmpLoja->user, $tmpLoja->password, $tmpLoja->banco_de_dados);
            
            $vendaSQL = "SELECT NOME_TIPO, DATA, SUM(TOTAL) AS TOTAL, CANCELADA, CODIGOVENDA  FROM venda WHERE data >= '" . $dataINI_inverse . "' AND data <= '" . $dataFIN_inverse . "' AND CANCELADA is null GROUP BY NOME_TIPO ORDER BY TOTAL DESC";
            
            $getTipos = $this->connectLojas->consulta($vendaSQL);       
            //echo $vendaSQL. " ++++++++++ ";     
            //var_dump($getVendas);
            foreach ($getTipos as $getTipo) {
                // Retornando valores em jSON Encode
                $this->itensjSON[$k]["tipopgto"] = $getTipo->NOME_TIPO;
                $this->itensjSON[$k]["total"]   = number_format($getTipo->TOTAL, 2, ',', '.');
                $auxTot+=$getTipo->TOTAL;
                $k++;
            }
         //monta o TOTAL
         $this->itensjSON[$k]["tipopgto"] = "TOTAL";
         $this->itensjSON[$k]["total"]   = number_format($auxTot, 2, ',', '.');            
        }
        
        // Saindo da conexão $connectLoja
        $this->connectLojas->Leave();

        echo json_encode($this->itensjSON, JSON_HEX_QUOT);
    }
    
    public function getLojasJSON() {
        $getDados = $this->getConWebLojas();
        // Conexão feita com o banco de dados exterior
        // Retornando valores para manipulação
        $k = 0;
        foreach ($getDados as $tmpLoja) {
            $this->itensjSON[$k]["ID"] = $tmpLoja->id;
            $this->itensjSON[$k]["DESCRICAO"] = $tmpLoja->descricao;
            $k++;
        }
        echo json_encode($this->itensjSON, JSON_HEX_QUOT);
    }
}