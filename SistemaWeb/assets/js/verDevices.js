var dataLog = [];
var dados_old = [];
var dados = [];
var target;

$(document).ready(function () {
    try {
        // essa função irá executar a cada alguns segundos para verificar alterações, se houver alguma irá recarregar a tabela

        // reseta a tabela

        // preenche a tabela com os novos dados
        dados_old = insertRow();

        var table = document.getElementsByTagName("table")[0];
        var tbody = table.getElementsByTagName("tbody")[0];
        tbody.onclick = function (e) {
            e = e || window.event;
            var data = [];
            target = e.srcElement || e.target;
            while (target && target.nodeName !== "TR") {
                target = target.parentNode;
            }
            if (target) {
                var cells = target.getElementsByTagName("td");
                for (var i = 0; i < cells.length; i++) {
                    data.push(cells[i].innerHTML);
                }
            }
            // mostra os dados da linha clicada
            dataLog = data;

            document.getElementById('modalBody2').innerHTML = "Ao alterar o SharedID será criado um novo link de compartilhamento e o link anterior deixará de funcionar, deseja prosseguir? ";


            };
    }catch(err){
        //console.log("cant load data yet");
    }
    // tempo entre as chamadas das requisições

});

function alterarCompartilhamento(){
    var table = document.getElementsByTagName("table")[0];
    var tbody = table.getElementsByTagName("tbody")[0];
    table.rows[target.rowIndex].cells[4].innerHTML  = "Alterando...";

    // força a sombra do modal a desaparecer
    $('#my_modal2').modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();

    $.ajax({
        url: "../private/page_cadastroDevice/alterarCompartilhamento.php",
        type: "get",
        data:{"device": dataLog[0]},
        success: function (result) {
            const response = jQuery.parseJSON(result);
            if(response["response"] === 200){
                $("#body").load("pages/cadastroDevices.html");
                md.showNotification('top','center', "ShareID Alterado com sucesso!", 3);
            }
            else{
                console.log(response);
                console.log("erro ao alterar ShareID");
            }
        }

    })
}



// insere as linhas na tabela sempre que algum dado for alterado
function insertRow() {


    $.ajax({
        url: "../private/page_cadastroDevice/listarDispositivos.php",
        type: "get",
        data: {"dispositivos": "todos"},
        success: function (result) {
            const response = jQuery.parseJSON(result);
            // verifica se a chamada da API foi bem sucedida
            if (response["response"] === 200) {
                // verifica se houve alteração nos dados durante essa requisição
                try {
                    $("#tableBodyDispositivos tr").remove();
                    var arrayLength = response["message"].length;
                    for (var i = 0; i < arrayLength; i++) {
                        var table = document.getElementById("tableBodyDispositivos");

                        tableContent = '<tr bgcolor="#C8E6C9">'
                        tableContent += "<td>" + response["message"][i]["nome"] + "</td>"
                            + "<td>" + response["message"][i]["HashKey"] + "</td>"
                            + "<td>" + response["message"][i]["displayKey"] + "</td>"
                            + "<td>" + response["message"][i]["timeCreation"] + "</td>"
                            + "<td>" + '<button class="btn btn-primary" href="#" data-target="#my_modal2" data-toggle="modal" data-id="my_id_value" ' +
                            ' id="btnAlterarCompartilhamento">Alterar Compartilhamento</button>' + "</td>"
                        tableContent += '</tr>';
                        table.innerHTML += tableContent;
                    }
                }catch(err){
                    //console.log("cant load data yet");
                }

            } else {
                $("#tableBodyDispositivos tr").remove();
                //console.log("erro ao recuperar dados");
            }
        }

    });
    return dados;

}

