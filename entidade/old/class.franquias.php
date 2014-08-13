<?php
class franquias {
    //Controles para o SELECT
    Public $page;
    Public $rows;
    Public $sort;
    Public $order;
    Public $offset;
    Public $total;
    //variaveis de conexao
    public $WLHostCFG = "";
    public $WLUserCFG = "";
    public $WLPassCFG = "";
    public $WLDBCFG = "";

    function franquias() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION ['USERID']) and ! isset($_SESSION ['NIVEL'])) {
            include ('../view/login.html');
        } elseif ($_SESSION ['NIVEL'] > 1000) {
            
            $this->page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $this->rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;

            include 'shared/conncfg.php';
            $this->WLHostCFG = $WLHostCFG;
            $this->WLUserCFG = $WLUserCFG;
            $this->WLPassCFG = $WLPassCFG;
            $this->WLDBCFG = $WLDBCFG;
        }
    }

    Public function get_franquias_grid($page) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $sql = "SELECT * FROM franquias ORDER BY nome asc LIMIT " . ($page * 10) . ",10";
        $rs = mysqli_query($conexao,$sql);

        $BrGridRow = '';
        while ($row = mysqli_fetch_object($rs)) {
            $BrGridRow .='<tr>'
                    . '<td>' . $row->id . '</td>'
                    . '<td>' . $row->nome . '</td>'
                    . '<td id="opcoes">'
                    . '<a href="#" onclick="pesqFranq(' . $row->id . ')" title="Alterar">Alterar</a> - '
                    . '<a href="#" onclick="deletaFranq(' . $row->id . ')" title="Apagar">Apagar</a>'
                    . '</td>'
                    . '</tr>';
        }
        $rs = mysqli_query($conexao,"SELECT COUNT(*) FROM franquias");
        $row = mysqli_fetch_row($rs);
        $this->total = $row[0];
        if ($page == 0) {
            $this->rows = $this->total;
        } else {
            $this->rows = $page * 10;
        }
        
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

    Public function get_franquias_select() {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $rs = mysqli_query($conexao, 'SELECT * FROM franquias');
        if ($rs) {
            $BrGridRow = '<select id="franquia" onchange="carregaLoja(this.value,1)" class="required">'
                    . '<option value="0">Todas as franquias</option>';
            while ($row = mysqli_fetch_object($rs)) {
                $BrGridRow = $BrGridRow . '<option value="' . $row->id . '">' . $row->nome . '</option>';
            }
            $BrGridRow = $BrGridRow . '</select>';
        } else {
            echo mysqli_error($conexao);
        }
        return $BrGridRow;
    }

    Public function salva($nome, $user) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "INSERT INTO franquias (nome,data_cad,user_cad) VALUES ('$nome',now(),'$user')";

        if (mysqli_query($conexao, $query)) {
            $retorno = "true";
        } else {
            $retorno = "Erro ao salvar a franquia!\n" + mysql_error();
        }
        return $retorno;
    }

    Public function deleta($id) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "DELETE FROM franquias WHERE id ='$id'";
        if (mysqli_query($conexao, $query)) {
            $retorno = "true";
        } else {
            $retorno = "Erro durante a exclusao!\n" + mysql_error();
        }
        return $retorno;
    }

    /* metodo q carrega a combo de franquias na pagina de cad. lojas e produtos */

    public function combo() {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $query = "SELECT id,nome FROM franquias";
        $consulta = mysqli_query($conexao, $query);
        if (mysqli_num_rows($consulta) > 0) {
            $retorno = array();
            $i = 0;
            while ($dados = mysqli_fetch_array($consulta)) {
                $retorno [$i] ["id"] = $dados ["id"];
                $retorno [$i] ["nome"] = $dados ["nome"];
                $i ++;
            }
            $teste = json_encode($retorno, JSON_HEX_QUOT);
        } else {
            echo "Nenhum valor encontardo";
        }
        return $teste;
    }

    public function pesqAltera($id) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $query = "SELECT * FROM franquias WHERE id = $id";
        $consulta = mysqli_query($conexao, $query);
        if (mysqli_num_rows($consulta) > 0) {
            $retorno = array();
            $i = 0;
            while ($dados = mysqli_fetch_array($consulta)) {
                $retorno [$i] ["id"] = $dados ["id"];
                $retorno [$i] ["nome"] = $dados ["nome"];
                $i ++;
            }
            $teste = json_encode($retorno, JSON_HEX_QUOT);
        }
        return $teste;
    }

    public function altera($nome, $user, $id) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $retorno;
        $query = "UPDATE franquias SET nome='$nome',user_cad='$user'  WHERE id = '$id'";
        if ($consulta = mysqli_query($conexao, $query)) {
            $retorno = "true";
        } else {
            $retorno = mysql_error();
        }
        return $retorno;
    }

}