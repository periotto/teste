<?php

class produtos {

    // Controles para o SELECT
    public $page;
    public $rows;
    public $prev;
    public $next;
    public $last;
    public $first;
    public $total;
    // Para fins de pesquisa de produtos de lojas ou franquias específicas
    public $IDLoja;
    public $IDFranquia;
    //variaveis de conexao
    public $WLHostCFG = "";
    public $WLUserCFG = "";
    public $WLPassCFG = "";
    public $WLDBCFG = "";

    function produtos() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION ['USERID']) and ! isset($_SESSION ['NIVEL'])) {
            include ('../view/login.html');
        } elseif ($_SESSION ['NIVEL'] > 1000) {
            // Para fins de pesquisa de produtos de lojas ou franquias específicas
            $this->IDLoja = isset($_POST ['IDLoja']) ? intval($_POST ['IDLoja']) : 0;
            $this->IDFranquia = isset($_POST ['IDFranquia']) ? intval($_POST ['IDFranquia']) : 0;

            include 'shared/conncfg.php';
            $this->WLHostCFG = $WLHostCFG;
            $this->WLUserCFG = $WLUserCFG;
            $this->WLPassCFG = $WLPassCFG;
            $this->WLDBCFG = $WLDBCFG;
        }
    }

    /* recebe o array contendo os 10 mais de cada loja
     * e retorna com os valores de produtos iguais somados */

    //ex: produto vendas
    //    nike    12
    //    nike    34
    //me retorna 12+34 e exclui um desses elementos
    //adidas == 7
    //fila == 13
    function soma_produtos($rows) {
        $aux = array();
        $array = $rows;
        $flag = false;
        $x = 0;
        for ($i = 0; $i < count($array); $i++) {
            if (isset($rows[$i])) {
                $nome = $rows[$i]["c"][0];
                for ($j = $i + 1; $j < count($array); $j++) {

                    if (isset($rows[$j])) {
                        $nome2 = $rows[$j]["c"][0];

                        if ($nome == $nome2) {
                            $flag = true;

                            $rows[$i]["c"][1]["v"] = (int) $rows[$i]["c"][1]["v"] + (int) $rows[$j]["c"][1]["v"];
                            unset($rows[$j]);

                            $aux[$x]["c"][0] = $nome;
                            $aux[$x]["c"][1] = $rows[$i]["c"][1];
                            $x++;
                        }
                    }
                }
            }

            if ($flag) {
                $flag = false;
            } else {
                if (isset($rows[$i])) {
                    $aux[$x]["c"][0] = $rows[$i]["c"][0];
                    $aux[$x]["c"][1] = $rows[$i]["c"][1];
                    $x++;
                }
            }
        }
        return $this->ordena_produtos($aux);
    }

    /* recebe o array contendo os 10 mais de cada loja
     * e retorna os 10 mais de todos os produtos */

    function ordena_produtos($rows) {
//        rows[12] . c[0] . v; //nome
//        rows[12] . c[1] . v; //valor
//        print_r($rows);
        $aux = 0;
        for ($i = 0; $i < count($rows); $i++) {
            for ($j = $i + 1; $j < count($rows); $j++) {

                if ($rows[$i]["c"][1] < $rows[$j]["c"][1]) {
                    $aux = $rows[$i];
                    $rows[$i] = $rows[$j];
                    $rows[$j] = $aux;
                }
            }
        }
        for ($i = 10; count($rows) > 10; $i--) {
            array_pop($rows);
        }
        return $rows;
    }

    public function get_grafico_produto($idLoja) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);

        $sql = "SELECT * FROM lojas WHERE id = $idLoja";
        if ((int) $idLoja == 0) {
            $sql = "SELECT * FROM lojas";
        }
        $consulta = mysqli_query($conexao, $sql);
        if ($consulta) {
            $retorno = array(
                'dados' => array(
                    'cols' => array(
                        array('type' => 'string', 'label' => 'Produto'),
                        array('type' => 'number', 'label' => 'Compras')
                    ),
                    'rows' => array()
                ),
                'config' => array(
                    'pieHole' => 0.4,
                    'width' => 490,
                    'height' => 355,
                    'legend' => 'bottom',
                    'title' => 'Top 10 - Os mais vendidos',
                    'backgroundColor' => 'transparent',
                )
            );
            while ($dados = mysqli_fetch_array($consulta)) {
                $host = $dados ["host"];
                $user = $dados ["user"];
                $senha = $dados ["password"];
                $banco = $dados ["banco_de_dados"];
                $conn = mysqli_connect($host, $user, $senha, $banco);
                $sql = "SELECT vi.CODIGOPRODUTO, p.DESCRICAO, COUNT(p.CODIGOPRODUTO) AS qtd FROM produto p "
                        . "JOIN venda_itens vi ON vi.CODIGOPRODUTO = p.CODIGOPRODUTO JOIN venda v ON "
                        . "vi.CODIGOVENDA = v.CODIGOVENDA GROUP BY p.CODIGOPRODUTO ORDER BY qtd DESC LIMIT 0, 10";
                $rs = mysqli_query($conn, $sql);
                if ($rs) {
                    while ($dados = mysqli_fetch_object($rs)) {
                        $retorno['dados']['rows'][] = array('c' => array(
                                array('v' => $dados->DESCRICAO),
                                array('v' => (int) $dados->qtd)
                        ));
                    }
                } else {
                    echo mysqli_error($conn);
                }
            }
            $retorno['dados']['rows'] = $this->soma_produtos($retorno['dados']['rows']);
            return json_encode($retorno);
        } else {
            //echo $loja;
        }
    }

    Public function get_produtos_grid($loja, $page) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $sql = "SELECT * FROM lojas WHERE id = $loja";
        $consulta = mysqli_query($conexao, $sql);
        if ($consulta) {
            while ($dados = mysqli_fetch_array($consulta)) {
                $host = $dados ["host"];
                $user = $dados ["user"];
                $senha = $dados ["password"];
                $banco = $dados ["banco_de_dados"];
            }
            $conn = mysqli_connect($host, $user, $senha, $banco);

            $BrGridRow = '';
            $sql = "SELECT * FROM produto p ORDER BY descricao ASC LIMIT " . ($page * 12) . ",12";
            $rs = mysqli_query($conn, $sql);
            if ($rs) {
                while ($dados = mysqli_fetch_array($rs)) {
                    $BrGridRow .=
                            '<tr>'
                            . '<td>' . $dados ["CODIGOPRODUTO"] . '</td>'
                            . '<td>' . $dados ["DESCRICAO"] . '</td>'
                            . '<td>' . $dados ["PRECOVENDA"] . '</td>'
                            . '<td>' . $dados["SALDOFISICO"] . '</td>'
                            . '<td>' . $dados ["UNIDADE"] . '</td>'
                            . '<td>' . $dados ["CODIGOBARRA"] . '</td>'
                            . '<td id="opcoes">'
                            . '<a href="#" onclick="alteraProduto(' . $dados ["CODIGOPRODUTO"] . ',' . $loja . ');" '
                            . 'title="Alterar">Alterar</a> - '
                            . '<a onclick="deletaProduto(' . $dados ["CODIGOPRODUTO"] . ',' . $loja . ');" href="#" '
                            . 'title="Apagar">Apagar</a>'
                            . '</td>'
                            . '</tr>';
                }
                $count = "SELECT COUNT(*) AS teste FROM produto p";
                $rs = mysqli_query($conn, $count);
                $dados = mysqli_fetch_array($rs);
            } else {
                echo mysqli_error($conn);
            }

            //verifica a página atual caso seja informada na URL, senão atribui como 1ª página
            $pagina = (isset($_GET['page'])) ? $_GET['page'] : 1;

            //seleciona todos os itens da tabela
            $cmd = "select * from produtos";
            $produtos = mysql_query($cmd);

            //conta o total de itens
            $total = mysql_num_rows($produtos);

            //seta a quantidade de itens por página, neste caso, 2 itens
            $registros = 2;

            //calcula o número de páginas arredondando o resultado para cima
            $numPaginas = ceil($total / $registros);

            //variavel para calcular o início da visualização com base na página atual
            $inicio = ($registros * $pagina) - $registros;

            //seleciona os itens por página
            $cmd = "select * from produtos limit $inicio,$registros";
            $produtos = mysql_query($cmd);
            $total = mysql_num_rows($produtos);

            //exibe os produtos selecionados
            while ($produto = mysql_fetch_array($produtos)) {
                echo $produto['id'] . " - ";
                echo $produto['nome'] . " - ";
                echo $produto['descricao'] . " - ";
                echo "R$ " . $produto['valor'] . "<br />";
            }

            //exibe a paginação
            for ($i = 1; $i < $numPaginas + 1; $i++) {
                echo "<a href='paginacao.php?pagina=$i'>" . $i . "</a> ";
            }


            return $BrGridRow;
        } else {
            //echo $loja;
        }
    }

    public function pesqAltera($id, $loja) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $sql = "SELECT * FROM lojas WHERE id = '$loja'";
        $consulta = mysqli_query($conexao, $sql);
        if ($consulta) {
            while ($dados = mysqli_fetch_array($consulta)) {
                $host = $dados ["host"];
                $user = $dados ["user"];
                $senha = $dados ["password"];
                $banco = $dados ["banco_de_dados"];
            }
            $conn = mysqli_connect($host, $user, $senha, $banco);
            $teste = "";
            $query = "SELECT * FROM produto WHERE CODIGOPRODUTO = $id";
            $consulta = mysqli_query($conn, $query);
            if (mysqli_num_rows($consulta) > 0) {
                $retorno = array();
                $i = 0;
                while ($dados = mysqli_fetch_array($consulta)) {
                    $retorno [$i]["ID"] = $dados ["CODIGOPRODUTO"];
                    $retorno [$i]["Origem"] = $dados ["CODIGOTRIBUTARIO_ORIGEM"];
                    $retorno [$i]["Exportacao"] = $dados ["CODIGOEXPORTACAO"];
                    $retorno [$i]["Codigo"] = $dados ["CODIGOTRIBUTARIO"];
                    $retorno [$i]["codigoBarra"] = $dados ["CODIGOBARRA"];
                    $retorno [$i]["ContaAnalitica"] = $dados ["COD_CTA"];
                    $retorno [$i]["tipoItem"] = $dados ["TIPO_ITEM"];
                    $retorno [$i]["Grupo"] = $dados ["CODIGOGRUPO"];
                    $retorno [$i]["Preco"] = $dados ["PRECOVENDA"];
                    $retorno [$i]["Mercosul"] = $dados ["COD_NCM"];
                    $retorno [$i]["Servicos"] = $dados ["COD_LST"];
                    $retorno [$i]["Unidade"] = $dados ["UNIDADE"];
                    $retorno [$i]["Receita"] = $dados ["RECEITA"];
                    $retorno [$i]["Desc"] = $dados ["DESCRICAO"];
                    $retorno [$i]["Genero"] = $dados ["COD_GEN"];
                    $retorno [$i]["ICMS"] = $dados ["ICMVENDA"];
                    $retorno [$i]["ISSQN"] = $dados ["ISSQN"];
                    $retorno [$i]["IPI"] = $dados ["EX_IPI"];
                    $retorno [$i]["Saldo"] = $dados ["SALDOFISICO"];

                    $i ++;
                }
                $teste = json_encode($retorno, JSON_HEX_QUOT);
            } else {
                echo mysqli_error($consulta);
            }
        } else {
            echo mysqli_error($conexao);
        }
        return $teste;
    }

    Public function salva($franquia, $genero, $idLoja, $codigoBarra, $desc, $unidade, $preco, $estoque, $issqn, $icms, $origem, $codigo, $tipoItem, $grupo, $exportacao, $ipi, $mercosul, $servicos, $contaAnalitica, $receita, $pesavel, $prodPropria, $promocao) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $sql = "SELECT * FROM lojas WHERE id_franquia = '$franquia'";
        $consulta = mysqli_query($conexao, $sql);
        if ($consulta) {
            $retorno = array();
            while ($dados = mysqli_fetch_array($consulta)) {
                $host = $dados ["host"];
                $user = $dados ["user"];
                $senha = $dados ["password"];
                $banco = $dados ["banco_de_dados"];

                $conn = mysqli_connect($host, $user, $senha, $banco);
                $query = "INSERT INTO produto (SALDOFISICO,CODIGOBARRA,DESCRICAO,UNIDADE,PRECOVENDA,ISSQN,ICMVENDA,"
                        . "CODIGOTRIBUTARIO_ORIGEM,CODIGOTRIBUTARIO,TIPO_ITEM,CODIGOGRUPO,CODIGOEXPORTACAO,EX_IPI,"
                        . "COD_GEN,COD_NCM,COD_LST,COD_CTA,RECEITA,PESAVEL,PRODUCAOPROPRIA,PROMOCAO,DATACADASTRO,"
                        . "HASH_TAB_PROD,HASH_ESTOQUE) VALUES('$estoque','$codigoBarra','$desc','$unidade','$preco"
                        . "','$issqn','$icms','$origem','$codigo','$tipoItem','$grupo','$exportacao','$ipi','$genero"
                        . "','$mercosul','$servicos','$contaAnalitica','$receita','$pesavel','$prodPropria"
                        . "','$promocao',now(),'VALOR X','VALOR Y')";
                if (mysqli_query($conn, $query)) {
                    $mens = "true";
                } else {
                    $mens = "Erro ao salvar produto\n" + mysql_error();
                }
            }
        } else {
            $mens = "Erro ao pesquisar. Mais de um(ou nenhum) resultados\n" . mysqli_error();
        }
        return $mens;
    }

    Public function deleta($id, $idLoja) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $sql = "SELECT * FROM lojas WHERE id = '$idLoja'";
        $consulta = mysqli_query($conexao, $sql);
        if ($consulta) {
            $retorno = array();
            while ($dados = mysqli_fetch_array($consulta)) {
                $host = $dados ["host"];
                $user = $dados ["user"];
                $senha = $dados ["password"];
                $banco = $dados ["banco_de_dados"];
            }
            $conn = mysqli_connect($host, $user, $senha, $banco);
            $query = "DELETE FROM produto WHERE CODIGOPRODUTO = '$id'";
            if (mysqli_query($conn, $query)) {
                $retorno = "true";
            } else {
                $retorno = "Erro durante a exclusao!\n" + mysqli_error();
            }
            return $retorno;
        }
    }

    public function altera($id, $genero, $idLoja, $codigoBarra, $desc, $unidade, $preco, $estoque, $issqn, $icms, $origem, $codigo, $tipoItem, $grupo, $exportacao, $ipi, $mercosul, $servicos, $contaAnalitica, $receita, $pesavel, $prodPropria, $promocao) {
        $conexao = mysqli_connect($this->WLHostCFG, $this->WLUserCFG, $this->WLPassCFG, $this->WLDBCFG);
        $sql = "SELECT * FROM lojas WHERE id = '$idLoja'";
        $consulta = mysqli_query($conexao, $sql);
        if ($consulta) {
            $retorno = array();
            while ($dados = mysqli_fetch_array($consulta)) {
                $host = $dados ["host"];
                $user = $dados ["user"];
                $senha = $dados ["password"];
                $banco = $dados ["banco_de_dados"];
            }
            $conn = mysqli_connect($host, $user, $senha, $banco);
            $query = "UPDATE produto SET SALDOFISICO='$estoque',CODIGOBARRA='$codigoBarra',DESCRICAO='$desc',"
                    . "UNIDADE='$unidade',PRECOVENDA='$preco',ISSQN='$issqn',ICMVENDA='$icms',"
                    . "CODIGOTRIBUTARIO_ORIGEM='$origem',CODIGOTRIBUTARIO='$codigo',TIPO_ITEM='$tipoItem',"
                    . "CODIGOGRUPO='$grupo',CODIGOEXPORTACAO='$exportacao',EX_IPI='$ipi',COD_GEN='$genero',"
                    . "COD_NCM='$mercosul',COD_LST='$servicos',COD_CTA='$contaAnalitica',RECEITA='$receita',"
                    . "PESAVEL='$pesavel',PRODUCAOPROPRIA='$prodPropria',PROMOCAO='$promocao' WHERE CODIGOPRODUTO = '$id'";
            if (mysqli_query($conn, $query)) {
                $mens = "true";
            } else {
                $mens = "Erro ao salvar produto\n" + mysql_error();
            }
        } else {
            $mens = "Erro ao pesquisar. Mais de um(ou nenhum) resultados\n" . mysqli_error();
        }
        return $mens;
    }

}
