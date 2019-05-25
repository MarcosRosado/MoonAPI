// função responsável pelo cadastro de produtos

$(function () {


    $('#cadastroDevice').on('submit', function (e) {

    e.preventDefault();

    var elem = document.getElementById("cadastrarDevice");
    elem.innerText = "Cadastrando...";
    $.ajax({
        type: 'POST',
        url: "../private/page_cadastroDevice/cadastroDevice.php",
        data: $('form').serialize(),
        success: function (response) {
            console.log(response);
            var resposta = jQuery.parseJSON(response);
            if (resposta['response'] === 200){
                md.showNotification('top','center', "Dispositivo cadastrado com sucesso!", 3);
                setTimeout($("#body").load("pages/cadastroDevices.html"), 3000);
            }
            else{
                md.showNotification('top','center', "Dispositivo já cadastrado.", 4);
                elem.innerText = "Cadastrar Dispositivo";
            }
        }
    });

});
});




