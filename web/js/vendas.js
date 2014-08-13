/*ESTE SCRIPT SÓ É USADO NA PAGINA DO MAPA*/
$("#mens").show();
var windows = false;
$(document).ready(function() {
    $(".sub-filtro table").css('margin', '0 2%');
    d = new Date;
    dia = d.getDate();
    month = d.getMonth() - 1 + 2;
    year = d.getUTCFullYear();
    agora = dia + '/' + month + '/' + year;

    $("#dataINI").val(agora);
    $("#dataFIN").val(agora);

    getProdutos();
    getLojas(0);
    $("#dataINI").datepicker({
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
        dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
        dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
            'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior'
    });
    $("#dataFIN").datepicker({
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
        dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
        dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
            'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior'
    });
    $("#mens").hide();
    $("#vendas").hide();
    $("#itens").hide();
});
/*-------------------------------Só uso isso-----------------------------*/
// AJAX JSON para retorno de LOJAS
// Essa é a chamada principal
function getLojas(idLoja) {
    var idFranquia = $("#franquia").val();
    var idLoja = $("#loja").val();
    console.log(idLoja)
    windows = false;
    var dataINI = $('#dataINI').val();
    var dataFIN = $('#dataFIN').val();
    $.ajax({
        data: {"idFranquia": idFranquia, "idLoja": idLoja, "dataINI": dataINI, "dataFIN": dataFIN},
        datatype: "json",
        type: "POST",
        url: "getMarkers.php",
        success: function(received_data) {
            //console.log(received_data);
            //Limpa os marcadores antigos para add os novos.
            $("#mapa").gmap3({
                clear: {},
            });
            var tabela = "<table class='table pesq'>" +
                    "<thead>" +
                    "<tr>" +
                    "<th>Loja</th>" +
                    "<th>Descricao</th>" +
                    "<th>Endereco</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>";
            var newData = $.parseJSON(received_data);
            for (var nCount = 0; nCount < newData.length; nCount++) {
                setMarkerObject(newData[nCount].DESCRICAO, newData[nCount].FRANQUIA, newData[nCount].ENDERECO,
                        newData[nCount].VALOR, newData[nCount].IDFRANQUIA, newData[nCount].LOJA);
                tabela += "<tr class='link_class' onclick='vendasM(" + newData[nCount].IDFRANQUIA + ","
                        + newData[nCount].LOJA + ",\"" + newData[nCount].DESCRICAO + "\");'>" +
                        "<td>" + newData[nCount].LOJA + "</td>" +
                        "<td>" + newData[nCount].DESCRICAO + "</td>" +
                        "<td>" + newData[nCount].ENDERECO + "</td>" +
                        "</tr>";
            }
            tabela += "</tbody></table>";
            $("#tLojas").html(tabela);
            getTiposPgto(idFranquia, idLoja, dataINI, dataFIN);
        }, error: function() {
            console.log('Erro na requisição de senha via AJAX.');
        },
        async: false
    });
}
//FUNCAO Q MONTA O GRAFICO NA PAGINA DE VENDAS
function getProdutos() {
    var idLoja = $('#loja').val();
    $("#torta").show();
    var json_text = $.ajax({
        url: "controls/crud/pesquisas.php?acao=graficoProduto",
        datatype: "json",
        type: "POST",
        data: {"Loja": idLoja},
        async: false
    }).responseText;
    console.log(json_text);
    var json = $.parseJSON(json_text);
    var dados = new google.visualization.DataTable(json.dados);
    var chart = new google.visualization.PieChart(document.getElementById('grafico'));
    chart.draw(dados, json.config);
}
// AJAX JSON para retorno de ITENS DE venda de uma loja
//getVendaItens(16537, 2, Loja 4);
function getVendaItens(codVenda, idFranquia, idLoja) {
    var dataINI = $('#dataINI').val();
    var dataFIN = $('#dataFIN').val();
//    console.log(dataINI,dataFIN,codVenda, idFranquia, idLoja);
    $.ajax({
        data: {"codVenda": codVenda, "idFranquia": idFranquia, "idLoja": idLoja, "dataINI": dataINI,
            "dataFIN": dataFIN},
        datatype: "json",
        type: "POST",
        url: "vendaItens.php",
        success: function(received_itens) {
            console.log(received_itens);
            var tabela = "<table class='table pesq'>" +
                    "<thead>" +
                    "<tr>" +
                    "<th>Produto</th>" +
                    "<th>Descricao</th>" +
                    "<th style='text-align:right;'>Valor</th>" +
                    "<th>Quantidade</th>" +
                    "<th>Total</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>";
            var newData = $.parseJSON(received_itens);
            for (var nCount = 0; nCount < newData.length; nCount++) {
                //tabela+="<tr onclick='vendasM("+newData[nCount].LOJA+", \""+newData[nCount].DESCRICAO+"\");'>"+                
                tabela += "<tr>" +
                        "<td>" + newData[nCount].produto + "</td>" +
                        "<td>" + newData[nCount].descricao + "</td>" +
                        "<td style='text-align:right;'>" + newData[nCount].valor + "</td>" +
                        "<td>" + newData[nCount].quantidade + "</td>" +
                        "<td>" + newData[nCount].total + "</td>" +
                        "</tr>";
            }
            tabela += "</tbody></table>";
            if (nCount === 0) {
                tabela = "<p style='text-align: center;'> N&atildeo foram encontrados itens para essa venda</p>";
            }
            $("#tItens").html(tabela);
        }, error: function() {
            console.log('Erro na requisição via AJAX.');
        }, async: false
    });
}

//AJAX JSON para retorno de VENDAS 
function getVendas(idFranquia, idLoja, dataINI, dataFIN) {

//    var dataINI = $('#dataINI').val();
//    var dataFIN = $('#dataFIN').val();
    var nCount = 0;
    $.ajax({
        data: {"idFranquia": idFranquia, "idLoja": idLoja, "dataINI": dataINI, "dataFIN": dataFIN},
        datatype: "json",
        type: "POST",
        url: "vendas.php",
        success: function(received_itens) {
            console.log(received_itens);
            var tabela = "<table class='table pesq'>" +
                    "<thead>" +
                    "<tr>" +
                    "<th>Cod. Venda</th>" +
                    "<th>Data</th>" +
                    "<th style='text-align:right;'>Valor</th>" +
                    "<th>Pagamento</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody style='overflow-y:scroll'>";
            var newData = $.parseJSON(received_itens);
            for (nCount; nCount < newData.length; nCount++) {
                tabela += "<tr  class='link_class' onclick='itensM(" + newData[nCount].codvenda + "," + idFranquia
                        + "," + idLoja + ");'><td>" + newData[nCount].codvenda + "</td>" +
                        "<td>" + newData[nCount].data + "</td>" +
                        "<td style='text-align:right;'>" + newData[nCount].valor + "</td>" +
                        "<td>" + newData[nCount].pgto + "</td>" +
                        "</tr>";
            }
            tabela += "</tbody></table>";
            if (nCount === 0) {
                tabela = "<p style='text-align: center; background-color: #fff;'> N&atildeo foram encontradas vendas"
                        + "no per&iacuteodo: <br>" + dataINI + " At&eacute; " + dataFIN + "</p>";
            }
            $("#tVendas").html(tabela);
        }, error: function() {
            console.log('Erro na requisição via AJAX.');
        }, async: false
    });
}

// Marca um Objeto ao gMaps
function setMarkerObject(NomeLoja, NomeFranquia, toAddress, infoWinMsg, idFranquia, idLoja) {
    $("#mapa").gmap3({
        map: {
            options: {
                center: [-15, -55],
                zoom: 4,
                action: 'setCenter'
            }
        },
        marker: {
            address: toAddress,
            events: {
                click: function(marker, event) {
                    datail_store(idFranquia, idLoja, NomeLoja, NomeFranquia, toAddress, infoWinMsg, windows)
                }
            }
        },
    });
}
function datail_store(idFranquia, idLoja, NomeLoja, NomeFranquia, toAddress, infoWinMsg, windows) {
    $("#mapa").gmap3({
        infowindow: {
            address: toAddress,
            options: {
                content:
                        '<div class="detail-store link_class" onclick="vendasM(' + idFranquia + ', ' + idLoja
                        + ', \'' + NomeLoja + '\');"><h3>' + NomeLoja + '</h3><h4>' + NomeFranquia + '</h4><p>'
                        + toAddress + '</p><p class="total-store"><b>Faturamento total:</b> R$ ' + infoWinMsg + '</p>' +
                        '<i>Clique para ver vendas</i>' +
                        '</div>',
            },
        }
    });
}

//AJAX JSON para retorno de Tipo das formas de pagamento juntamente com o valor de cada 
function getTiposPgto(idFranquia, idLoja, dataINI, dataFIN) {
    $.ajax({
        data: {"idFranquia": idFranquia, "idLoja": idLoja, "dataINI": dataINI, "dataFIN": dataFIN},
        datatype: "json",
        type: "POST",
        url: "tipopgto.php",
        success: function(received_itens) {
            //console.log(received_itens);
            var newData = $.parseJSON(received_itens);
            var tabela = "";
            var total;
            var dinheiro = 0;
            var pre = 0;
            var pedido = 0;
            var funcionario = 0;
            var cheque = 0;
            var cartao = 0;
            var crediario = 0;
            var vale = 0;
            var tipo;
            var t;
            for (var nCount = 0; nCount < newData.length; nCount++) {
                tipo = newData[nCount].tipopgto;
                var valor = newData[nCount].total;
                valor = valor.replace(",", ".");
                var v = parseFloat(valor);
                if (tipo === "Dinheiro") {
                    dinheiro += v;
                } else if (tipo === "Cheque - Pr") {
                    pre += v;
                } else if (tipo === "Cheque") {
                    cheque += v;
                } else if (tipo === "Cartao" || tipo === "TEF" || tipo === "Dinheiro\/TEF") {
                    cartao += v;
                } else if (tipo === "Crediario") {
                    crediario += v;
                } else if (tipo === "Vale") {
                    vale += v;
                } else if (tipo === "Pedido") {
                    pedido += v;
                } else if (tipo === "Funcionario") {
                    funcionario += v;
                } else if (tipo === "TOTAL") {
                    t = newData[nCount].total;
                    total = "<li id='total'><b>Total: <span id='valor'>R$ " + newData[nCount].total +
                            "<span></b></li>";
                }
            }
            if (t === "0,00") {
                tabela = "";
            } else {
                if (dinheiro !== 0) {
                    tabela += "<li id='pay-cash'>Dinheiro: <br />" +
                            "<b><span id='valor'>R$ " + dinheiro + "<span></b></li>";
                }
                if (cheque !== 0) {
                    tabela += "<li id='pay-check'>Cheque: <br />" +
                            "<b><span id='valor'>R$ " + cheque + "<span></b></li>";
                }
                if (cartao !== 0) {
                    tabela += "<li id='pay-card'>Cart&atildeo: <br />" +
                            "<b><span id='valor'>R$ " + cartao + "<span></b></li>";
                }
                if (crediario !== 0) {
                    tabela += "<li id='pay-billet'>Crediario: <br />" +
                            "<b><span id='valor'>R$ " + crediario + "<span></b></li>";
                }
                if (vale !== 0) {
                    tabela += "<li id='pay-gift'>Vale: <br />" +
                            "<b><span id='valor'>R$ " + vale + "<span></b></li>";
                }
                if (pedido !== 0) {
                    tabela += "<li id='pay-changegift'>Pedidos: <br />" +
                            "<b><span id='valor'>R$ " + pedido + "<span></b></li>";
                }
                if (funcionario !== 0) {
                    tabela += "<li id='pay-employee'>Funcionarios: <br />" +
                            "<b><span id='valor'>R$ " + funcionario + "<span></b></li>";
                }
                if (pre !== 0) {
                    tabela += "<li id='pay-datecheck'>Cheque - Pr&eacute;: <br />" +
                            "<b><span id='valor'>R$ " + pre + "<span></b></li>";
                }
            }
            var inicio = "<ul >" + total + tabela + "</ul>";
            $("#sidebar").html(inicio);
            $("#tipoPGTO").html(inicio);
        }, error: function() {
            console.log('Erro na requisição via AJAX.');
        }, async: false
    });
}

var listWindow = [];
function itensM(codVenda, idFranquia, idLoja) {
    if (checkWindow(codVenda)) {
        $.window.prepare({
            dock: 'bottom', // change the dock direction: 'left', 'right', 'top', 'bottom'
            dockArea: $('#janelaVendas')
        });
        getVendaItens(codVenda, idFranquia, idLoja);
        myWindowTmp = $.window({
            showModal: false,
            x: -1, // the x-axis value on screen, if -1 means put on screen center
            y: -1,
            width: 600,
            height: 160,
            modalOpacity: 0.5,
            createRandomOffset: {x: 200, y: 150},
            title: "Itens da venda: " + codVenda,
            content: $("#itens").html(), // load window_block2 html content
            footerContent: "",
            onOpen: function(wnd) {  // a callback function while container is added into body
                addWindow(codVenda, wnd);
            },
            onClose: function(wnd) { // a callback function while user click close button
                deleteWindow(codVenda);
            }
        });
    }
}

function vendasM(idFranquia, idLoja, descLoja) {
    var dataINI = $('#dataINI').val();
    var dataFIN = $('#dataFIN').val();
//    console.log(idFranquia, idLoja, descLoja);
    if (checkWindow(idLoja)) {
        $.window.prepare({
            dock: 'bottom', // change the dock direction: 'left', 'right', 'top', 'bottom'
            dockArea: $('#janelaVendas')
        });
        getVendas(idFranquia, idLoja, dataINI, dataFIN);
        $.window({
            showModal: false,
            createRandomOffset: {x: 200, y: 150},
            x: -1, // the x-axis value on screen, if -1 means put on screen center
            y: -1,
            width: 700,
            height: 385,
            modalOpacity: 0.5,
            title: descLoja,
            content: $("#vendas").html(), // load window_block2 html content
            footerContent: "Clique para visualizar os itens de cada venda.",
            onOpen: function(wnd) {  // a callback function while container is added into body
                addWindow(idLoja, wnd);
            },
            onClose: function(wnd) { // a callback function while user click close button
                deleteWindow(idLoja);
            }
        });
    }
}


function checkWindow(cID) {
    var mustOpen = true;
    $.each(listWindow, function(cIndex, myEntry) {
        if (myEntry[0] === cID) {
            //console.log(cID);
            mustOpen = false;
            //myEntry[1].minimize;
            myEntry[1].restore();
            //return false;
        }
    });
    return mustOpen;
}
function deleteWindow(cID) {
    $.each(listWindow, function(cIndex, myEntry) {
        if (myEntry[0] === cID) {
            listWindow.splice(cIndex, 1);
            return true;
        }
    });
}
function addWindow(cID, cWnd) {
    var tmpArr = new Array(cID, cWnd);
    listWindow.push(tmpArr);
    return true;
}