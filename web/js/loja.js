var mens = "Todos os campos em vermelho tem de ser preenchidos.";
$(document).ready(function() {
    $('.menu-anchor').on('click touchstart', function(e) {
        $('html').toggleClass('menu-active');
        e.preventDefault();
    });
    $(".direito").on('click touchstart', function(e) {
        $('html').removeClass('menu-active');
    });
})
/*----------------------------------FRANQUIA----------------------------------*/
function salvaFranq() {
    var id = $("#id").val();
    var franquia = $("#nome").val();

    if (franquia === "") {

        $("#fmFran2 p").html("<p style='color: red;margin: -1em 0 2em 0;font-size: 18px;'>" + mens + "</p>");
        $("#nome").css("border", "1px solid #ff0000");
    } else {
        if (id === "") {
            $.ajax({
                data: {"Nome": franquia},
                datatype: "json",
                type: "POST",
                url: "controls/crud/salva.php?acao=franquia",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Salvo com sucesso!");
                        window.location.href = "request.php?IDPAGINA=franquias&page=0";
                    } else {
                        alert("Erro ao salvar Franquia!");
                    }
                }
            });
        } else {
            $.ajax({
                data: {"ID": id, "Nome": franquia},
                datatype: "json",
                type: "POST",
                url: "controls/crud/altera.php?acao=franquia",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Alterado com sucesso!");
                        window.location.href = "request.php?IDPAGINA=franquias&page=0";
                    } else {
                        alert("Erro ao alterar Franquia!");
                    }
                }
            });
        }
    }
}

function deletaFranq(id) {
    var apagar = confirm('Deseja realmente excluir este registro?');
    if (apagar) {
        var ID = id;
        $.ajax({
            data: {"ID": ID},
            datatype: "json",
            type: "POST",
            url: "controls/crud/deleta.php?acao=franquia",
            success: function(data) {
                console.log(data);
                if (data === "true") {
                    alert("Excluido com sucesso!");
                    window.location.href = "request.php?IDPAGINA=franquias&page=0";
                } else {
                    alert("Erro ao excluir!");
                    window.location.href = "request.php?IDPAGINA=franquias&page=0";
                }
            }
        });
    } else {
        event.preventDefault();
    }
}

function pesqFranq(id) {
    cad();
    $.ajax({
        data: {"ID": id},
        datatype: "json",
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=pesqFranq",
        success: function(data) {
            console.log(data);
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            var combo = Array();
            for (var nCount = 0; nCount < x; nCount++) {
                combo[1] = newData[nCount].nome;
                combo[2] = newData[nCount].id;
            }
            $("h2").html("Alterar Franquia");
            $(".cad").find("#nome").val(combo[1]);
            $(".cad").find("#id").val(combo[2]);
        }
    });
}
/*preenche a combo das franquias na pagina de cad. lojas*/
function comboFranquia() {
    $.ajax({
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=comboFranquia",
        success: function(data) {
            console.log(data);
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            var combo = "<select id='franquia' name='franquia'"
                    + " onchange='carregaLoja(this.value)><option value='0'>Selecione a Franquias</option>";
            for (var nCount = 0; nCount < x; nCount++) {
                combo += "<option value=" + newData[nCount].id + ">" + newData[nCount].nome + "</option>";
            }
            combo += "</select>";
            console.log(combo);
            $("#franquia").html(combo);
            $("#new-store #fm #franquia").html(combo);
        }
    });
}
/*----------------------------------LOJA--------------------------------------*/
function salvaLoja() {
    var loja = $("#loja").val();
    var franquia = $("#franquia").val();
    var servidor = $("#servidor").val();
    var porta = $("#porta").val();
    var user = $("#user").val();
    var base = $("#base").val();
    var senha = $("#senha").val();
    var id = $("#id").val();
    var retorno = false;
    console.log(loja);
    if (loja === "") {
        $("#loja").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (senha === "") {
        $("#senha").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (franquia === "") {
        $("#franquia").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (porta === "") {
        $("#porta").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (servidor === "") {
        $("#servidor").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (user === "") {
        $("#user").css("border", "1px solid #ff0000");
        retorno = true;
    }
    if (base === "") {
        $("#base").css("border", "1px solid #ff0000");
        retorno = true;
    }

    if (retorno) {
        $("#fm p").html("<p style='color: red;margin: 0em 0 1em 0;font-size: 18px;'>" + mens + "</p>");
    } else {
        if (id === "") {
            $.ajax({
                data: {"Descricao": loja, "Franquia": franquia, "Servidor": servidor, "Porta": porta, "User": user,
                    "Senha": senha, "Base": base},
                datatype: "json",
                type: "POST",
                url: "controls/crud/salva.php?acao=loja",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Salvo com sucesso!");
                        window.location.href = "request.php?IDPAGINA=lojas&page=0";
                    } else {
                        console.log("Erro ao salvar");
                    }
                }
            });
        } else {
            $.ajax({
                data: {"ID": id, "Descricao": loja, "Franquia": franquia, "Servidor": servidor, "Porta": porta,
                    "User": user, "Senha": senha, "Base": base},
                datatype: "json",
                type: "POST",
                url: "controls/crud/altera.php?acao=loja",
                success: function(data) {
                    console.log(data);
                    if (data === "true") {
                        alert("Alterado com sucesso!");
                        window.location.href = "request.php?IDPAGINA=lojas&page=0";
                    } else {
                        console.log("Erro ao Alterar");
                    }
                }
            });

        }
    }
}

function deletaLoja(id) {
    var apagar = confirm('Deseja realmente excluir este registro?');
    if (apagar) {
        $.ajax({
            data: {"ID": id},
            datatype: "json",
            type: "POST",
            url: "controls/crud/deleta.php?acao=loja",
            success: function(data) {
                console.log(data);
                if (data === "true") {
                    alert("Excluido com sucesso!");
                    window.location.href = "request.php?IDPAGINA=lojas&page=0";
                } else {
                    alert("Erro ao excluir!");
                    window.location.href = "request.php?IDPAGINA=lojas&page=0";
                }
            }
        });
    } else {
        event.preventDefault();
    }
}

function pesqLoja(id) {
    cad();
    $.ajax({
        data: {"ID": id},
        datatype: "json",
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=pesqLoja",
        success: function(data) {
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            var combo = Array();
            for (var nCount = 0; nCount < x; nCount++) {
                combo[1] = newData[nCount].descricao;
                combo[2] = newData[nCount].port;
                combo[3] = newData[nCount].password;
                combo[4] = newData[nCount].user;
                combo[5] = newData[nCount].id;
                combo[6] = newData[nCount].host;
                combo[7] = newData[nCount].id_franquia;
                combo[8] = newData[nCount].banco_de_dados;
            }
            console.log(data)
            $("h2").html("Alterar Loja");
            $(".cad").find("#loja").val(combo[1]);
            $(".cad").find("#porta").val(combo[2]);
            $(".cad").find("#senha").val(combo[3]);
            $(".cad").find("#user").val(combo[4]);
            $(".cad").find("#id").val(combo[5]);
            $(".cad").find("#servidor").val(combo[6]);
            $(".cad").find("#franquia").val(combo[7]);
            $(".cad").find("#base").val(combo[8]);
        }
    });
}
function carregaLoja(id) {
    $.ajax({
        data: {"ID": id},
        datatype: "json",
        type: "POST",
        url: "controls/crud/pesquisas.php?acao=comboLojas",
        success: function(data) {
            ///console.log(data);
            var combo = "<select id=lojas' name='loja' class='required'>"
                    + "<option value='0'>Todas as lojas</option>";
            var newData = $.parseJSON(data);
            var x = $(newData).size();
            for (var nCount = 0; nCount < x; nCount++) {
                combo += "<option value=" + newData[nCount].id + ">" + newData[nCount].nome + "</option>";
            }
            combo += "</select>";
            $("#loja").html(combo);
        }
    });
}
/*----------------------------------USER--------------------------------------*/
