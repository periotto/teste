<?php
include_once('../controle/Conexao.php');
include_once '../dao/PermissaoDao.php';

class UserDao {
    public $c = null;
    public function UserDao() {
        $this->c = new Conexao();
    }
/*----------------------------------------------------------------------------*/
    public function salvar($user, $permissao) {
        $retorno = "";
        try {
            $verifica = $this->verificaSalva($user->getLogin());
            if ($verifica == "salva") {
                $psmt = $this->c->prepare("INSERT INTO user (login,senha,dataCad,userCad,nome,status)"
                        . " VALUES (?,?,?,?,?,?)");
                $psmt->bindValue(1, $user->getLogin());
                $psmt->bindValue(2, $user->getSenha());
                $psmt->bindValue(3, $user->getDataCad());
                $psmt->bindValue(4, $user->getUser());
                $psmt->bindValue(5, $user->getNome());
                $psmt->bindValue(6, $user->getStatus());
                if ($psmt->execute()) {
                    $pdao = new PermissaoDao();
                    $permissao->user = $this->c->lastInsertId();
                    if ($pdao->salvar($permissao)) {
                        $retorno = "true";
                        $_SESSION["user"] = "salvo";
                        $this->c = null;
                    } else {
                        $retorno = "p";
                    }
                } else {
                    print_r($psmt->errorInfo());
                }
            } else {
                $retorno = "mens";
            }
        } catch (PDOException $ex) {
            $retorno = "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function alterar($user, $permissao) {
        try {
            $verifica = $this->verificaAltera($user->getLogin(), $user->getId());
            if ($verifica == "salva") {
                $sql = $this->c->prepare("UPDATE user SET login=?,senha=?,dataCad=?,userCad=?"
                        . ",nome=? WHERE id=?");
                $psmt = $sql;
                $psmt->bindValue(1, $user->getLogin());
                $psmt->bindValue(2, $user->getSenha());
                $psmt->bindValue(3, $user->getDataCad());
                $psmt->bindValue(4, $user->getUser());
                $psmt->bindValue(5, $user->getNome());
                $psmt->bindValue(6, $user->getId());
                if ($psmt->execute()) {
                    $pdao = new PermissaoDao();
                    if ($pdao->alterar($permissao)) {
                        $retorno = "true";
                        $_SESSION["user"] = "alterado";
                        $this->c = null;
                    } else {
                        $retorno = "p";
                    }
                } else {
                    print_r($psmt->errorInfo());
                }
            } else {
                $retorno = "mens";
            }
        } catch (PDOException $ex) {
            echo "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function pesqAll($pagina) {
        try {
            $teste = '';
            $inicio = (13 * $pagina) - 13;
            $psmt = $this->c->prepare("SELECT * FROM user ORDER BY nome ASC LIMIT $inicio,13");
            
            if ($psmt->execute()) {
                $retorno = array();
                $i = 0;
                if ($psmt->rowCount() > 0) {
                    while ($dados = $psmt->fetchObject()) {
                        $retorno [$i] ["id"] = $dados->id;
                        $retorno [$i] ["login"] = $dados->login;
                        $retorno [$i] ["email"] = $dados->email;
                        $retorno [$i] ["nome"] = $dados->nome;
                        $i++;
                    }
                    $psmt = $this->c->query("SELECT COUNT(*) AS total FROM user");
                    $dados = $psmt->fetchObject();
                    $retorno ["total"] = $dados->total;
                    $retorno ["totalPaginas"] = ceil($dados->total / 13);
                    if((int)$pagina != 1){
                        $retorno ["totalItens"] = $pagina * 13;
                    }else{
                        $retorno ["totalItens"] = $i;
                    }
                    $teste = json_encode($retorno, JSON_HEX_QUOT);
                } else {
                    $teste = "vazio";
                }
            } else {
                print_r($psmt->errorInfo());
            }
        } catch (PDOException $ex) {
            $teste = "Erro: " . $ex->getMessage();
        }
        return $teste;
    }
/*--------------------------------------------------------------------------*/
    public function pesqId($id) {
        try {
            $psmt = $this->c->prepare("SELECT * FROM user WHERE id = ?");
            $psmt->bindValue(1, $id);
            if ($psmt->execute()) {
                while ($dados = $psmt->fetchObject()) {
                    $_SESSION["login"] = $dados->login;
                    $_SESSION["status"] = $dados->status;
                    $_SESSION["nome"] = $dados->nome;
                    $_SESSION["senha"] = $dados->senha;
                    $_SESSION["id"] = $dados->id;
                }
                $pdao = new PermissaoDao();
                //pega as permissoes do usuario para a tela de alteracao
                $permissao = $pdao->pesqPermissoes($_SESSION["id"]);
                $teste = json_encode($permissao, JSON_HEX_QUOT);
            } else {
                print_r($psmt->errorInfo());
            }
        } catch (PDOException $ex) {
            $teste = "Erro: " . $ex->getMessage();
        }
        return $teste;
    }
/*----------------------------------------------------------------------------*/
    public function deletar($id) {
        try {
            $psmt = $this->c->prepare("DELETE FROM user WHERE id=?");
            $psmt->bindValue(1, $id);

            if ($psmt->execute()) {
                $this->c = null;
                $retorno = "true";
                $_SESSION["user"] = "excluido";
            } else {
                print_r($psmt->errorInfo());
            }
        } catch (PDOException $ex) {
            $retorno = "Erro: " . $ex->getMessage();
        }
        return $retorno;
    }
/*--------------------------------------------------------------------------*/
    function verificaSalva($login) {
        $retorno = "";
        try {
            $psmt = $this->c->prepare("SELECT * FROM user WHERE login = ?");
            $psmt->bindValue(1, $login);
            if ($psmt->execute()) {
                if ($psmt->rowCount() == 0) {
                    $retorno = "salva";
                } else {
                    $retorno = "mens";
                }
            } else {
                print_r($psmt->errorInfo());
            }
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
        return $retorno;
    }
/*--------------------------------------------------------------------------*/
/*-------------- METODO Q VERIFICA SE O LOGIN JÁ É UTILIZADO ---------------*/
/*------------EM ALGUM OUTRO REGISTRO NA HORA Q VAI ALTERAR ----------------*/
/*--------------------------------------------------------------------------*/
    function verificaAltera($login, $id) {
        $retorno = "";
        try {
            $psmt = $this->c->prepare("SELECT * FROM user WHERE login = ?");
            $psmt->bindValue(1, $login);
            if ($psmt->execute()) {
                if ($psmt->rowCount() == 0) {
                    $retorno = "salva";
                } else {
                    $retorno = "mens";
                    while ($dados = $psmt->fetch()) {
                        switch ($id) {
                            case $dados["id"]:
                                $retorno = "salva";
                                break;
                            default :
                                $retorno = $retorno;
                                break;
                        }
                    }
                }
            } else {
                print_r($psmt->errorInfo());
            }
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
        return $retorno;
    }
/*----------------------------------------------------------------------------*/
    public function login($usuario) {
        try {
            $psmt = $this->c->prepare("SELECT * FROM user WHERE login=? AND senha=?");
            $psmt->bindValue(1, $usuario->getLogin());
            $psmt->bindValue(2, $usuario->getSenha());
            $teste = "";
            if ($psmt->execute()) {
                if ($psmt->rowCount() > 0) {
                    $dados = $psmt->fetchObject();
                    $_SESSION["id_user"] = $dados->id;
                    $_SESSION["nome"] = $dados->nome;
                    $_SESSION["ultimo_login"] = $dados->ultimo_login;

                    $pdao = new PermissaoDao();
                    $pdao->pesqPermissoesLogin($dados->id);
                    
                    //atualiza o ultimo login no banco
                    $psmt = $this->c->prepare("UPDATE user SET ultimo_login = NOW() WHERE id = ?");
                    $psmt->bindValue(1, $dados->id);
                    if($psmt->execute()){
                        $teste = "true";
                    }
                } else {
                    $teste = "login";
                }
            } else {
                print_r($psmt->errorInfo());
            }
        } catch (PDOException $ex) {
            $teste = "Erro: " . $ex->getMessage();
        }
        return $teste;
    }
}