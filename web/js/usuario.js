var mens = "Todos os campos em vermelho tem de ser preenchidos.";
$(document).ready(function() {
    $('.menu-anchor').on('click touchstart', function(e) {
        $('html').toggleClass('menu-active');
        e.preventDefault();
    });
    $(".direito").on('click touchstart', function(e) {
        $('html').removeClass('menu-active');
    });
    pesq(1);
    permissao();
});
/*----------------------------------------------------------------------------*/
/*------- VERIFICA ASESSAO DO USUARIO E PREENCHE ALGUNS CAMPOS(NOME) ---------*/
/*----------------------------------------------------------------------------*/
function permissao() {
    $.ajax({
        type: "POST",
        url: "../controle/SessaoControle.php",
        success: function(data) {
            console.log(data);
            var sessao = $.parseJSON(data);
            if (sessao.sessao === "false") {
                window.location.href = "../index.html";
            } else if (sessao.cad_user === "0") {
                alert("Você não tem as permissões necessárias para acessar esta página");
                window.location.href = "index.html";
            } else {
                var usuarios = '';
                var lojas = '';
                var franquias = '';
                var vendas = '';
                var produtos = '';
                var permissao = '';
                
                if (sessao.ver_vendas === "1") {
                    permissao += "<li><i>Ver vendas</i></li>";
                    vendas = '<li id="roxo">'
                            +'<a href="../controle/request.php?PAGINA=vendas">Vendas</a></li>';
                }
                if (sessao.cad_user) {
                    permissao += "<li><i>Cadastrar usu&aacute;rios</i></li>";
                    usuarios = '<li id="laranja">'
                            +'<a href="../controle/request.php?PAGINA=usuarios">Usu&aacute;rios</a></li>';
                }
                if (sessao.cad_franquia) {
                    permissao += "<li><i>Cadastrar franquias</i></li>";
                    franquias = '<li id="rosa">'
                            +'<a href="../controle/request.php?PAGINA=franquias">Franquias</a></li>';
                }
                if (sessao.cad_lojas) {
                    permissao += "<li><i>Cadastrar lojas</i></li>";
                    lojas = '<li id="azul">'
                            +'<a href="../controle/request.php?PAGINA=lojas">Lojas</a></li>';
                }
                if(sessao.cad_produto){
                    permissao += "<li><i>Cadastrar produtos</i></li>";
                    produtos = '<li id="verde">'
                            +'<a href="../controle/request.php?PAGINA=produtos">Produtos</a></li>';
                }
                $(".submenu").html(vendas+produtos+franquias+lojas+usuarios);
                $("#name").html(sessao.nome);
                $("#ultimo_login").html(sessao.ultimo_login);
                $("#permissoes").html(permissao);
                $("").html();
            }
        }
    });
}
/*----------------------------------------------------------------------------*/
function pesq(PAGINA) {
    $.ajax({
        datatype: "json",
        type: "POST",
        data: {"Pagina": PAGINA},
        url: "../controle/UsuarioControle.php?action=pesq",
        success: function(data) {
            var tabela = '';
            //variaveis responsaveis pela PAGINAcao
            var first = '';
            var last = '';
            var next = '';
            var prev = '';

            var newData = $.parseJSON(data);
            console.log(newData);

            $("#total").html(newData.total);
            $("#pag").html(PAGINA + "/" + newData.totalPaginas);
            $("#itens").html(newData.totalItens);

            for (contador = 0; contador <= $(newData).size(); contador++) {
                tabela += '<tr>'
                        + '<td id="id">' + newData[contador].id + '</td>'
                        + '<td>' + newData[contador].nome + '</td>'
                        + '<td>' + newData[contador].email + '</td>'
                        + '<td>' + newData[contador].login + '</td>'
                        + '<td id="opcoes">'
                        + '<a href="#" onclick="pesqUser(' + newData[contador].id
                        + ');" title="Alterar">Alterar</a> - '
                        + '<a href="#" onclick="deletaUser(' + newData[contador].id
                        + ');" title="Apagar">Apagar</a>'
                        + '</td>'
                        + '</tr>';
            }

            if (PAGINA === 1) {
                prev = '<li id="page-previous" class="disabled"><a></a>';
                next = '<a href="#" onclick="next(' + (PAGINA + 1) + ');"></a>';
                last = '<li id="page-last"><a href="#" onclick="last(' + newData.totalPaginas + ');"></a>';
            }
            if (PAGINA === newData.totalPaginas) {
                if (PAGINA === 1) {
                    next = '<li id="page-next" class="disabled"><a></a>';
                    last = '<li id="page-last" class="disabled"><a></a>';
                    first = '<li id="page-first" class="disabled"><a></a>';
                    prev = '<li id="page-previous" class="disabled"><a></a>';
                } else {
                    next = '<li id="page-next" class="disabled"><a></a>';
                    last = '<li id="page-last" class="disabled"><a></a>';

                    prev = '<li id="page-previous"><a href="#" onclick="previous(' + (PAGINA - 1) + ');"></a>';
                    first = '<li id="page-first"><a href="#" onclick="first(1);"></a>';
                }
            }

            if ((PAGINA > 1) && (PAGINA < newData.totalPaginas)) {
                prev = '<li id="page-previous"><a href="#" onclick="previous(' + (PAGINA - 1) + ');"></a>';
                first = '<li id="page-first"><a href="#" onclick="first(1);"></a>';
                next = '<li id="page-next"><a href="#" onclick="next(' + (PAGINA + 1) + ');"></a>';
                last = '<li id="page-last"><a href="#" onclick="last(' + newData.totalPaginas + ');"></a>';
            }
            $("#page-first").replaceWith(first);
            $("#page-previous").replaceWith(prev);
            $("#page-next").replaceWith(next);
            $("#page-last").replaceWith(last);

            $("#DATAGRID").html(tabela);
//            $("#mobile").html(mobile);
        }
    });
}
/*----------------------------------------------------------------------------*/
function cad() {
    $("#mensagens").hide();
    $(".cad").load("forms.html #new-user");
    $(".table-footer").remove();
    $(".table").remove();
}
/*----------------------------------------------------------------------------*/
function salvaUser() {
    var nome = $("#nome").val();
    var user = $("#user").val();
    var email = $("#email").val();
    var senha = $("#senha").val();
    var id = $("#id").val();
    var confSenha = $("#senha2").val();
    var retorno = false;

    if (nome === "") {
        $("#nome").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (email === "") {
        $("#email").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (senha === "") {
        $("#senha").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (user === "") {
        $("#user").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (confSenha === "") {
        $("#senha2").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (confSenha !== senha) {
        mens = "Senha e confirmacao tem de ser iguais";
        retorno = true;
    }

    if (retorno) {
        $("#fmUser p").html("<p style='color: red;font-size: 18px;'>" + mens + "</p>");
    } else {
        /*array q recebe os valores dos checkbox da tela de cadUser*/
        var x = new Array();
        var y = new Array();

        /* metodo .is() retorna um boolen*/
        x[0] = $("#cadUsers").is(':checked');
        x[1] = $("#cadLojas").is(':checked');
        x[2] = $("#cadFranquias").is(':checked');
        x[3] = $("#cadProdutos").is(':checked');
        x[4] = $("#verVendas").is(':checked');

        /*verifica se checkbox esta selecionado e passa valores(1=selecionado ou 0=nao selecionado) para
         *  salvar permissão de user*/
        for (contador = 0; contador < 5; contador++) {
            if (x[contador] === true) {
                y[contador] = "1";
            } else {
                y[contador] = "0";
            }
        }
        if (id === "") {//salva
            $.ajax({
                data: {"Email": email, "Nome": nome, "User": user, "Senha": senha, "cadUser": y[0],
                    "cadLojas": y[1], "cadFranquias": y[2],
                    "cadProd": y[3], "cadVendas": y[4]},
                datatype: "json",
                type: "POST",
                url: "controls/crud/salva.php?acao=user",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Salvo com sucesso!");
                        window.location.href = "request.php?IDPAGINA=usuarios";
                    } else if (data === "email") {
                        $("#mensagens").html("Erro ao salvar User!\nEmail já existe");
                    } else if (data === "login") {
                        $("#mensagens").html("Erro ao salvar User!\nUsuário já existe");
                    }
                }
            });
        } else {//altera
            $.ajax({
                data: {"ID": id, "Email": email, "Nome": nome, "User": user, "Senha": senha,
                    "cadUser": y[0], "cadLojas": y[1], "cadFranquias": y[2], "cadProd": y[3], "cadVendas": y[4]},
                datatype: "json",
                type: "POST",
                url: "controls/crud/altera.php?acao=user",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Alterado com sucesso!");
                        window.location.href = "request.php?IDPAGINA=usuarios&page=0";
                    } else if (data === "email") {
                        $("#mensagens").html("Erro ao alterar User!\nEmail ja existe");
                    } else if (data === "login") {
                        $("#mensagens").html("Erro ao alterar User!\nLogin ja existe");
                    }
                }
            });
        }
    }
}
/*----------------------------------------------------------------------------*/
function deletaUser(id) {
    var apagar = confirm('Deseja realmente excluir este registro?');
    if (apagar) {
        var ID = id;
        $.ajax({
            data: {"ID": ID},
            datatype: "json",
            type: "POST",
            url: "controls/crud/deleta.php?acao=user",
            success: function(data) {
                console.log(data);
                if (data === "true") {
                    alert("Excluido com sucesso");
                    window.location.href = "request.php?IDPAGINA=usuarios";
                } else {
                    alert("Erro ao excluir");
                }
            }, error: function() {
                console.log('Erro na requisição via AJAX.');
            }, async: false
        });
    } else {
        event.preventDefault();
    }
}
/*----------------------------------------------------------------------------*/
function pesqUser(id) {
    cad();
    $.ajax({
        data: {"ID": id},
        datatype: "json",
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=pesqUser",
        success: function(data) {
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            var combo = Array();
            for (var nCount = 0; nCount < x; nCount++) {
                combo[1] = newData[nCount].nome;
                combo[2] = newData[nCount].email;
                combo[3] = newData[nCount].password;
                combo[4] = newData[nCount].username;
                combo[5] = newData[nCount].id;
            }
            $("h2").html("Alterar Usuario");
            $(".cad").find("#nome").val(combo[1]);
            $(".cad").find("#email").val(combo[2]);
            $(".cad").find("#senha").val(combo[3]);
            $(".cad").find("#senha2").val(combo[3]);
            $(".cad").find("#user").val(combo[4]);
            $(".cad").find("#id").val(id);
        }
    });
}
/*-------------------------------------------------------------------*/
function login() {
    var login = $("#user").val();
    var senha = $("#pass").val();
    var verifica = false;

    if (senha === "") {
        verifica = true;
    }
    if (login === "") {
        verifica = true;
    }
    if (verifica) {
        $(".info span").html("Campos Obrigatórios");
        $(".info p").css("margin", "0");
        $(".info p").css("font-size", "15px");
        $(".info").fadeIn("slow");
    } else {
        $.ajax({
            datatype: "json",
            type: "POST",
            data: {"Senha": senha, "Login": login},
            url: "controle/UsuarioControle.php?action=login",
            success: function(data) {
                console.log(data);
                if (data === "login") {
                    $(".info").hide();

                    $(".erro p").html("Login e/ou senha incorretos!");
                    $(".erro span").html("Erro");
                    $(".erro p").css("margin", "0");
                    $(".erro p").css("font-size", "15px");
                    $(".erro").fadeIn("slow");
                } else if (data === "true") {
                    window.location.href = "Web/usuarios.html";
                }
            }
        });
    }
}