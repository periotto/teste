<?php
class lojas {
    // Controles para o SELECT
    public $page;
    public $rows;
    public $sort;
    public $order;
    public $offset;
    public $total;
    // Para fins de pesquisa de produtos de lojas ou franquias específicas
    public $IDLoja;
    public $IDFranquia;
    //variaveis de conexao
    public $WLHostCFG = "";
    public $WLUserCFG = "";
    public $WLPassCFG = "";
    public $WLDBCFG = "";

    function lojas() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION ['USERID']) and ! isset($_SESSION ['NIVEL'])) {
            include ('./view/login.html');
        } elseif ($_SESSION ['NIVEL'] > 1000) {

            $this->page = isset($_POST ['page']) ? intval($_POST ['page']) : 1;
            $this->rows = isset($_POST ['rows']) ? intval($_POST ['rows']) : 10;
            $this->offset = ($this->page - 1) * $this->rows;


            $this->IDLoja = isset($_POST [' IDLoja']) ? intval($_POST ['IDLoja']) : 0;
            $this->IDFranquia = isset($_POST ['IDFranquia']) ? intval($_POST ['IDFranquia']) : 0;

            include ("shared/conncfg.php");
            $this->WLHostCFG = $WLHostCFG;
            $this->WLUserCFG = $WLUserCFG;
            $this->WLPassCFG = $WLPassCFG;
            $this->WLDBCFG = $WLDBCFG;
        }
    }

    Public function get_lojas_grid($page) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $sql = "SELECT * FROM lojas ORDER BY descricao asc LIMIT " . ($page * 10) . ",10";
        $rs = mysqli_query($conexao, $sql);

        $BrGridRow = '';
        while ($row = mysqli_fetch_object($rs)) {
            $BrGridRow .= '<tr>'
                    . '<td>' . $row->id . '</td>'
                    . '<td>' . $row->descricao . '</td>'
                    . $this->get_endereco($row)
                    . '<td id="opcoes">'
                    . '<a href="#" onclick="pesqLoja(' . $row->id . ');" title="Alterar">Alterar</a> - '
                    . '<a href="#" onclick="deletaLoja(' . $row->id . ');" title="Apagar">Apagar</a>'
                    . '</td>'
                    . '</tr>';
        }
        $rs = mysqli_query($conexao,"SELECT COUNT(*) FROM lojas");
        $row = mysqli_fetch_row($rs);
        $this->total = $row [0];
        $this->rows = $page * 10;

        //responsavel pela paginação
        $this->page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($this->page == 0) {
            $this->first = '<li id="page-first" class="disabled"><a></a></li>';

            $this->prev = '<li id="page-previous" class="disabled"><a></a></li>';
        } else if ((int) $this->page > 0) {
            $this->prev = '<li id="page-previous"><a href="#" onclick="previous('
                    . $loja . ',' . ($this->page - 1) . ');"></a></li>';

            $this->first = '<li id="page-first"><a href="#" onclick="first('
                    . $loja . ',0);"></a></li>';
        }
        if ((($page * 10) + 10) < $this->total) {
            $this->next = '<li id="page-next"><a href="#" onclick="next('
                    . $loja . ',' . ($this->page + 1) . ');"></a></li>';

            $this->last = '<li id="page-last"><a href="#" onclick="last('
                    . $loja . ',' . substr($this->total, 0, -1) . ');"></a></li>';
        } else {
            $this->next = '<li id="page-next" class="disabled"><a></a></li>';
            $this->last = '<li id="page-last" class="disabled"><a></a></li>';
        }
        $this->page = "".$this->page."/".floor((int)$this->total/10);
        return $BrGridRow;
    }
    function get_endereco($loja) {
        $conexao = mysqli_connect($loja->host, $loja->user, $loja->password, $loja->banco_de_dados);

        $sql = "SELECT * FROM loja";
        $rs = mysqli_query($conexao, $sql);

        $BrGridRow = '';
        while ($row = mysqli_fetch_object($rs)) {
            $BrGridRow .= '<td>'.$row->ENDERECO.' , '.$row->NUMERO.' , '.$row->CIDADE.'</td>';
        }
        return $BrGridRow;
    }
    Public function salva($host, $port, $user, $banco_de_dados, $password, $id_franquia, $user_cad, $descricao) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "INSERT INTO lojas (host,port,user,banco_de_dados,password,id_franquia,user_cad,descricao,data_cad)"
                . " VALUES('$host','$port','$user','$banco_de_dados','$password','$id_franquia','$user_cad"
                . "','$descricao',now())";
        if (mysqli_query($conexao, $query)) {
            $retorno = "true";
        } else {
            $retorno = "Erro ao salvar Loja\n" + mysql_error();
        }
        return $retorno;
    }

    Public function deleta($id) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "DELETE FROM lojas WHERE id='$id'";
        if (mysqli_query($conexao, $query)) {
            $retorno = "true";
        } else {
            $retorno = "Erro durante a exclusao!\n" + mysqli_error();
        }
        return $retorno;
    }

    Public function altera($id, $loja, $franquia, $servidor, $porta, $user, $senha, $base, $user_cad) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "UPDATE lojas SET descricao='$loja', banco_de_dados='$base', id_franquia='$franquia', host='$servidor',"
                . "port='$porta', user='$user',user_cad='$user_cad', password='$senha' WHERE id='$id'";
        if (mysqli_query($conexao, $query)) {
            $retorno = "true";
        } else {
            $retorno = "Erro ao alterar Loja\n" + mysql_error();
        }
        return $retorno;
    }

    public function pesqAltera($id) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "SELECT * FROM lojas WHERE id = $id";
        $consulta = mysqli_query($conexao, $query);
        if (mysqli_num_rows($consulta) > 0) {
            $retorno = array();
            $i = 0;
            while ($dados = mysqli_fetch_array($consulta)) {
                $retorno [$i] ["id"] = $dados ["id"];
                $retorno [$i] ["descricao"] = $dados ["descricao"];
                $retorno [$i] ["port"] = $dados ["port"];
                $retorno [$i] ["user"] = $dados ["user"];
                $retorno [$i] ["password"] = $dados ["password"];
                $retorno [$i] ["host"] = $dados ["host"];
                $retorno [$i] ["banco_de_dados"] = $dados ["banco_de_dados"];
                $retorno [$i] ["id_franquia"] = $dados ["id_franquia"];
                $i ++;
            }
            $teste = json_encode($retorno, JSON_HEX_QUOT);
        }
        return $teste;
    }

    public function combo($id) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        if ($id == 0) {
            $query = "SELECT id,descricao FROM lojas";
        } else {
            $query = "SELECT id,descricao FROM lojas WHERE id_franquia = '$id'";
        }
        $consulta = mysqli_query($conexao, $query);
        if (mysqli_num_rows($consulta) > 0) {
            $retorno = array();
            $i = 0;
            while ($dados = mysqli_fetch_array($consulta)) {
                $retorno [$i] ["id"] = $dados ["id"];
                $retorno [$i] ["nome"] = $dados ["descricao"];
                $i ++;
            }
            $teste = json_encode($retorno, JSON_HEX_QUOT);
        } else {
            echo "Nenhum valor encontardo";
        }
        return $teste;
    }

}