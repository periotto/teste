<?php
include_once('../controle/Conexao.php');
include_once('../entidade/class.permissao.php');

class PermissaoDao {
    public $c = null;
    public function PermissaoDao() {
        $this->c = new Conexao();
    }
/*----------------------------------------------------------------------------*/
    public function salvar($permissao) {
        $retorno = false;
        try {
            $psmt = $this->c->prepare("INSERT INTO permissao (id_user, cad_lojas, cad_users, cad_franquia,"
                    . "cad_produto, ver_vendas) VALUES (?,?,?,?,?,?)");
            $psmt->bindValue(1, $permissao->getId_user());
            $psmt->bindValue(2, $permissao->getCad_lojas());
            $psmt->bindValue(3, $permissao->getCad_user());
            $psmt->bindValue(4, $permissao->getCad_franquia());
            $psmt->bindValue(5, $permissao->getCadProduto());
            $psmt->bindValue(6, $permissao->getVer_vendas());

            if ($psmt->execute()) {
                $retorno = true;
            } else {
                print_r($psmt->errorInfo());
            }
            $this->c = null;
        } catch (PDOException $ex) {
            $retorno = "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function deletar($id) {
        $retorno = false;
        try {
            $psmt = $this->c->prepare("DELETE FROM permissao WHERE idUser=?");
            $psmt->bindValue(1, $id);

            if ($psmt->execute()) {
                $this->c = null;
                $retorno = true;
            }
        } catch (PDOException $ex) {
            $retorno = "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function alterar($permissao) {
        $retorno = false;
        try {
            if ($this->deletar($permissao->getUser())) {
                if ($this->salvar($permissao)) {
                    $retorno = true;
                }
            }
        } catch (PDOException $ex) {
            $retorno = "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function pesqPermissoesLogin($idUser) {
        $retorno = array();
        try {
            $psmt = $this->c->prepare("SELECT * FROM permissao WHERE id_user = ?");
            $psmt->bindValue(1, $idUser);
            if ($psmt->execute()) {
                $dados = $psmt->fetch();
                /* itens responsaveis pelo menu */
                $_SESSION["cad_user"] = $dados["cad_users"];
                $_SESSION["cad_lojas"] = $dados["cad_lojas"];
                $_SESSION["cad_franquia"] = $dados["cad_franquia"];
                $_SESSION["cad_produto"] = $dados["cad_produto"];
                $_SESSION["ver_vendas"] = $dados["ver_vendas"];
            }else {
                print_r($psmt->errorInfo());
            }
        } catch (PDOException $ex) {
            echo "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function pesqPermissoes($idUser) {
        $retorno = array();
        $especifica = array();
        try {
            $psmt = $this->c->prepare("SELECT * FROM permissao WHERE idUser=? ORDER BY campEspecifica ASC");
            $psmt->bindValue(1, $idUser);
            $psmt->execute();
            $i = 0;
            while ($dados = $psmt->fetch()) {

                $retorno[$i]["verCampanhaInativa"] = $dados["verCampanhaInativa"];
                $retorno[$i]["campEspecifica"] = (int) $dados["campEspecifica"];
                $retorno[$i]["verUserInativo"] = $dados["verUserInativo"];
                $retorno[$i]["cadPergunta"] = $dados["cadPergunta"];
                $retorno[$i]["cadCampanha"] = $dados["cadCampanha"];
                $retorno[$i]["verCampanha"] = $dados["verCampanha"];
                $retorno[$i]["cadUser"] = $dados["cadUser"];
                $retorno[$i]["id"] = $dados["id"];
                $i++;
            }
        } catch (PDOException $ex) {
            echo "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
}
?>