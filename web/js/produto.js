var mens = "Todos os campos em vermelho tem de ser preenchidos.";
$(document).ready(function() {
    $('.menu-anchor').on('click touchstart', function(e) {
        $('html').toggleClass('menu-active');
        e.preventDefault();
    });
    $(".direito").on('click touchstart', function(e) {
        $('html').removeClass('menu-active');
    });
    permissao();
});
/*----------------------------------------------------------------------------*/
//funções reponsaveis pela paginação
function pesqProduto(PAGINA) {
    var loja = $("#loja").val();
    if (+loja === 0) {
        alert("Escolha uma loja antes de pesquisar");
    } else {
        $.ajax({
            datatype: "json",
            type: "POST",
            data: {"Pagina": PAGINA,"Loja":loja},
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
            }
        });
    }
}
function first(loja, pagina) {
    window.location.href = "request.php?IDPAGINA=produtos&loja=" + loja + "&page=" + pagina;
}
function previous(loja, pagina) {
    window.location.href = "request.php?IDPAGINA=produtos&loja=" + loja + "&page=" + pagina;
}
function next(loja, pagina) {
    window.location.href = "request.php?IDPAGINA=produtos&loja=" + loja + "&page=" + pagina;
}
function last(loja, pagina) {
    window.location.href = "request.php?IDPAGINA=produtos&loja=" + loja + "&page=" + pagina;
}
/*----------------------------------------------------------------------------*/
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
            } else if (sessao.cad_produto === "0") {
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
                            + '<a href="../controle/request.php?PAGINA=vendas">Vendas</a></li>';
                }
                if (sessao.cad_user) {
                    permissao += "<li><i>Cadastrar usu&aacute;rios</i></li>";
                    usuarios = '<li id="laranja">'
                            + '<a href="../controle/request.php?PAGINA=usuarios">Usu&aacute;rios</a></li>';
                }
                if (sessao.cad_franquia) {
                    permissao += "<li><i>Cadastrar franquias</i></li>";
                    franquias = '<li id="rosa">'
                            + '<a href="../controle/request.php?PAGINA=franquias">Franquias</a></li>';
                }
                if (sessao.cad_lojas) {
                    permissao += "<li><i>Cadastrar lojas</i></li>";
                    lojas = '<li id="azul">'
                            + '<a href="../controle/request.php?PAGINA=lojas">Lojas</a></li>';
                }
                if (sessao.cad_produto) {
                    permissao += "<li><i>Cadastrar produtos</i></li>";
                    produtos = '<li id="verde">'
                            + '<a href="../controle/request.php?PAGINA=produtos">Produtos</a></li>';
                }
                $(".submenu").html(vendas + produtos + franquias + lojas + usuarios);
                $("#name").html(sessao.nome);
                $("#ultimo_login").html(sessao.ultimo_login);
                $("#permissoes").html(permissao);
                $("").html();
            }
        }
    });
}
/*----------------------------------------------------------------------------*/
function salvaProduto() {
    var codigoBarra = $("#codigoBarra").val();
    var idLoja = $("#lojas").val();
    var franquia = $("#franquia").val();
    var desc = $("#desc").val();
    var unidade = $("#unidade").val();
    var preco = $("#preco").val();
    var estoque = $("#estoque").val();
    var issqn = $("#issqn").val();
    var icms = $("#icms").val();
    var origem = $("#origem").val();
    var codigo = $("#codigo").val();
    var tipoItem = $("#tipoItem").val();
    var grupo = $("#grupo").val();
    var exportacao = $("#exportacao").val();
    var ipi = $("#ipi").val();
    var mercosul = $("#mercosul").val();
    var servicos = $("#servicos").val();
    var contaAnalitica = $("#contaAnalitica").val();
    var receita = $("#receita").val();
    var genero = $("#genero").val();
    var id = $("#id").val();

    var retorno = false;
    $(".required").css("border", "1px solid #bbbbbb");

    if (tipoItem === "") {
        $("#tipoItem").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (grupo === "") {
        $("#grupo").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (servicos === "") {
        $("#servicos").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (genero === "") {
        $("#genero").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (codigo === "") {
        $("#codigo").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (origem === "") {
        $("#origem").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (icms === "") {
        $("#icms").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (issqn === "") {
        $("#issqn").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (estoque === "") {
        $("#estoque").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (preco === "") {
        $("#preco").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (unidade === "") {
        $("#unidade").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (desc === "") {
        $("#desc").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (codigoBarra === "") {
        $("#codigoBarra").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (+franquia === 0) {
        $("#lojas").css("border", "1px solid #ff0000");
        retorno = true;
    }

    if (retorno) {
        $("#new-product p").html(mens);
        $("#new-product p").css('color', 'red');
        $("#new-product p").css('font-size', '18px');
    } else {
        /*array q recebe os valores dos checkbox da tela de cadProduto*/
        var x = new Array();
        var y = new Array();

        /* metodo .is() retorna um boolen*/
        x[0] = $("#pesavel").is(':checked');
        x[1] = $("#prodPropria").is(':checked');
        x[2] = $("#promocao").is(':checked');

        /*verifica se checkbox esta selecionado e passa valores(1=selecionado ou 0=nao selecionado)
         *  para salvar permissão de user*/
        for (i = 0; i < 3; i++) {
            if (x[i] === true) {
                y[i] = "S";
            } else {
                y[i] = "N";
            }
        }
        if (id === "") {
            $.ajax({
                data: {"Franquia": franquia, "idLoja": idLoja, "codigoBarra": codigoBarra, "Desc": desc,
                    "Unidade": unidade, "Preco": preco, "Estoque": estoque, "ISSQN": issqn, "ICMS": icms,
                    "Origem": origem, "Codigo": codigo, "tipoItem": tipoItem, "Grupo": grupo, "Exportacao": exportacao,
                    "IPI": ipi, "Mercosul": mercosul, "Servicos": servicos, "Genero": genero,
                    "ContaAnalitica": contaAnalitica, "Receita": receita, "Pesavel": y[0], "ProdPropria": y[1],
                    "Promocao": y[2]},
                datatype: "json",
                type: "POST",
                url: "controls/crud/salva.php?acao=produto",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Salvo com sucesso!");
                        window.location.href = "request.php?IDPAGINA=combos";
                    } else {
                        alert("Erro ao salvar Produto!");
                    }
                }
            });
        } else {
            $.ajax({
                data: {"ID": id, "idLoja": idLoja, "codigoBarra": codigoBarra, "Desc": desc, "Unidade": unidade,
                    "Preco": preco, "Estoque": estoque, "ISSQN": issqn, "ICMS": icms, "Origem": origem,
                    "Codigo": codigo, "tipoItem": tipoItem, "Grupo": grupo, "Exportacao": exportacao, "IPI": ipi,
                    "Mercosul": mercosul, "Servicos": servicos, "Genero": genero, "ContaAnalitica": contaAnalitica,
                    "Receita": receita, "Pesavel": y[0], "ProdPropria": y[1], "Promocao": y[2]},
                datatype: "json",
                type: "POST",
                url: "controls/crud/altera.php?acao=produto",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Alterado com sucesso!");
                        window.location.href = "request.php?IDPAGINA=combos";
                    } else {
                        alert("Erro ao alterar Produto!");
                    }
                }
            });
        }
    }
}
function alteraProduto(id, loja) {
    cad();
    $.ajax({
        data: {"ID": id, "Loja": loja},
        datatype: "json",
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=pesqProduto",
        success: function(data) {
            console.log(data);
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            var combo = Array();
            for (var nCount = 0; nCount < x; nCount++) {
                combo [0] = newData[nCount].Origem;
                combo [1] = newData[nCount].Exportacao;
                combo [2] = newData[nCount].Codigo;
                combo [3] = newData[nCount].codigoBarra;
                combo [4] = newData[nCount].ContaAnalitica;
                combo [5] = newData[nCount].tipoItem;
                combo [6] = newData[nCount].Grupo;
                combo [7] = newData[nCount].Preco;
                combo [8] = newData[nCount].Mercosul;
                combo [9] = newData[nCount].Servicos;
                combo [10] = newData[nCount].Unidade;
                combo [11] = newData[nCount].Receita;
                combo [12] = newData[nCount].Desc;
                combo [13] = newData[nCount].Genero;
                combo [14] = newData[nCount].ICMS;
                combo [15] = newData[nCount].ISSQN;
                combo [16] = newData[nCount].IPI;
                combo [17] = newData[nCount].ID;
                combo [18] = newData[nCount].Saldo;
            }
            console.log(combo);
            $("h3").html("Alterar Produto");
            $(".cad").find("#origem").val(combo[0]);
            $(".cad").find("#exportacao").val(combo[1]);
            $(".cad").find("#codigo").val(combo[2]);
            $(".cad").find("#codigoBarra").val(combo[3]);
            $(".cad").find("#contaAnalitica").val(combo[4]);
            $(".cad").find("#tipoItem").val(combo[5]);
            $(".cad").find("#grupo").val(combo[6]);
            $(".cad").find("#preco").val(combo[7]);
            $(".cad").find("#mercosul").val(combo[8]);
            $(".cad").find("#servicos").val(combo[9]);
            $(".cad").find("#unidade").val(combo[10]);
            $(".cad").find("#receita").val(combo[11]);
            $(".cad").find("#desc").val(combo[12]);
            $(".cad").find("#genero").val(combo[13]);
            $(".cad").find("#icms").val(combo[14]);
            $(".cad").find("#issqn").val(combo[15]);
            $(".cad").find("#ipi").val(combo[16]);
            $(".cad").find("#id").val(combo[17]);
            $(".cad").find("#estoque").val(combo[18]);
        }
    });
}
function deletaProduto(id, loja) {
    var apagar = confirm('Deseja realmente excluir este registro?');
    if (apagar) {
        $.ajax({
            data: {"ID": id, "Loja": loja},
            datatype: "json",
            type: "POST",
            url: "controls/crud/deleta.php?acao=produto",
            success: function(data) {
                console.log(data);
                if (data === "true") {
                    alert("Excluido com sucesso");
                    window.location.href = "request.php?IDPAGINA=produtos&loja=" + loja + "&page=0";
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
function cad() {
    $("#mensagens").hide();
    $(".cad").load("view/forms.html #new-product");
    $(".table-footer").remove();
    $(".table").remove();
    comboFranquia();
}
function carregaLoja(id, metodo) {
    $.ajax({
        data: {"ID": id},
        datatype: "json",
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=comboLojas",
        success: function(data) {
            var combo = "<select id=lojas' name='loja' class='required'>";
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            for (var nCount = 0; nCount < x; nCount++) {
                combo += "<option value=" + newData[nCount].id + ">" + newData[nCount].nome + "</option>";
            }
            combo += "</select>";
            if (metodo === 2) {
                $("#new-product #lojas").html(combo);
            } else {
                $("#loja").html(combo);
            }
        }
    });
}
function comboFranquia() {
    $.ajax({
        data: {"Pagina": 0},
        datatype: "json",
        type: "POST",
        url: "controle/FranquiaControle.php?action=pesq",
        success: function(data) {
            console.log(data);
            
        }
    });
}