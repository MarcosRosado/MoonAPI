function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

function getDados(paramVal){
    $.ajax({
        url:"../private/page_visualizar/visualizar.php",
        type:"get",
        data:{"shareId":paramVal},
        success:function (result) {
            const response = jQuery.parseJSON(result);
            if (response['response'] === 200){
                for (i = 0; i < response['message'].length; i++) {
                    console.log(response['message'][i]);
                }
                //alert(response['message'][0]['valor']);
            }
            else{
                console.log('Esse shareId não existe, foi modificado ou não possui nenhum dado, consulte o fornecedor para receber o novo link');
                alert('Esse shareId não existe, foi modificado ou não possui nenhum dado, consulte o fornecedor para receber o novo link');
            }
        }
    })

}


$(document).ready(function () {
    var paramVal = findGetParameter("shareId");
    getDados(paramVal);


});