var DataArray = [];
var Data;

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
                for (let i = 0; i < response['message'].length; i++) {
                    var temp = [];
                    for (var key in response['message'][i]){
                        temp.push(response['message'][i][key]);
                    }
                    DataArray.push(temp);
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

function downloadData(){


    let csvContent = "data:text/csv;charset=utf-8,";
        DataArray.forEach(function(rowArray) {
            let temp = rowArray.join(",");
            csvContent += temp + "\r\n";
        });



    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "dados_"+Date.now()+".csv");
    document.body.appendChild(link); // Required for FF

    link.click(); // This will download the data file named "my_data.csv".
}

$(document).ready(function () {
    var paramVal = findGetParameter("shareId");
    getDados(paramVal);
    setInterval(function() {
        DataArray = [];
        getDados(paramVal);
        updateLineChart();
    }, 30 * 1000); // 60 * 1000 milsec

    $('#downloadData').click(function(){
        downloadData();
    });

});

