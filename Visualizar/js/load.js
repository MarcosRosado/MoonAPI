var DataArray = []; // array contendo todos os dados
var SensorArrayTemp = []; // lista dos sensores de temperatura
var SensorArrayUmid = []; // lista dos sensores de umidade
let arr = []; // array temporário para processamento dos dados
let fullDataUmid = []; // dados processados de umidade para o gráfico
let fullDataTemp = []; // ||       ||       || temperatura      ||  ||  ||

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

function getDados(paramVal){ // carrega os dados, e as listas de sensores
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
    });

    $.ajax({
        url:"../private/page_loaddata/loaddata.php",
        type:"get",
        data:{"shareId":paramVal, "type": "Temp"},
        success:function (result) {
            const response = jQuery.parseJSON(result);
            if (response['response'] === 200){
                for (let i = 0; i < response['message'].length; i++) {
                    var temp = [];
                    for (var key in response['message'][i]){
                        SensorArrayTemp.push(response['message'][i][key]);
                    }
                }
                //alert(response['message'][0]['valor']);
            }
            else{
                console.log('Esse shareId não existe, foi modificado ou não possui nenhum dado, consulte o fornecedor para receber o novo link');
                alert('Esse shareId não existe, foi modificado ou não possui nenhum dado, consulte o fornecedor para receber o novo link');
            }
        }
    });

    $.ajax({
        url:"../private/page_loaddata/loaddata.php",
        type:"get",
        data:{"shareId":paramVal, "type": "Umid"},
        success:function (result) {
            const response = jQuery.parseJSON(result);
            if (response['response'] === 200){
                for (let i = 0; i < response['message'].length; i++) {
                    var temp = [];
                    for (var key in response['message'][i]){
                        SensorArrayUmid.push(response['message'][i][key]);
                    }
                }
                //alert(response['message'][0]['valor']);
            }
            else{
                console.log('Esse shareId não existe, foi modificado ou não possui nenhum dado, consulte o fornecedor para receber o novo link');
                alert('Esse shareId não existe, foi modificado ou não possui nenhum dado, consulte o fornecedor para receber o novo link');
            }
        }
    });


}
function loadUmiGraph(){ // processa os dados do gráfico de umidade
    arr = [];
    for (let i = 0; i < SensorArrayUmid.length; i++){
        arr[i] = [];
    }
    let someDate = new Date();
    let curTimestamp = Math.floor(someDate.getTime()/1000); // pega o epoch atual

    // moldes de tempo para exibir no gráfico
    let h12 = 43200; // tempo em horas de meio dia‬
    let m20 = 1200;

    for (let i = 0; i < DataArray.length; i ++){
        if (parseInt(DataArray[i][2]) + h12 >= curTimestamp) { // dados das ultimas 12 horas
            for (let elem in SensorArrayUmid) {
                if (SensorArrayUmid[elem] === DataArray[i][3]) {
                    let d = new Date(parseInt(DataArray[i][2]) * 1000);
                    d.toLocaleString();
                    let year = d.getFullYear();
                    let hours = d.getHours();
                    let mins = d.getMinutes();
                    let day = d.getDate();
                    let month = d.getMonth() + 1;
                    let data = Date.UTC(year, month, day, hours, mins);

                    arr[elem].push([data, parseFloat(DataArray[i][0])]);
                    break;
                }
            }
        }

    }
    for (let elem in SensorArrayUmid){
        fullDataTemp.push({ name: SensorArrayUmid[elem],data: arr[elem]});

    }

}

function loadTempGraph(){ // processa os dados do gráfico de temperatura
    arr = [];
    for (let i = 0; i < SensorArrayTemp.length; i++){
        arr[i] = [];
    }
    let someDate = new Date();
    let curTimestamp = Math.floor(someDate.getTime()/1000); // pega o epoch atual

    // moldes de tempo para exibir no gráfico
    let h12 = 43200; // tempo em horas de meio dia‬
    let m20 = 1200;

    for (let i = 0; i < DataArray.length; i ++){
        if (parseInt(DataArray[i][2]) + h12 >= curTimestamp) { // dados das ultimas 12 horas
            for (let elem in SensorArrayTemp) {
                if (SensorArrayTemp[elem] === DataArray[i][3]) {
                    let d = new Date(parseInt(DataArray[i][2]) * 1000);
                    d.toLocaleString();
                    let year = d.getFullYear();
                    let hours = d.getHours();
                    let mins = d.getMinutes();
                    let day = d.getDate();
                    let month = d.getMonth() + 1;
                    let data = Date.UTC(year, month, day, hours, mins);

                    arr[elem].push([data, parseFloat(DataArray[i][0])]);
                    break;
                }
            }
        }

    }
    for (let elem in SensorArrayUmid){
        fullDataUmid.push({ name: SensorArrayTemp[elem],data: arr[elem]});

    }

}


function downloadData(){


    let csvContent = "data:text/csv;charset=utf-8,";
        DataArray.forEach(function(rowArray) {
            console.log(rowArray);
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
    setTimeout(function () { // gera os gráficos
        loadTempGraph();
        loadUmiGraph();
    }, 1000);


    $('#downloadData').click(function(){
        downloadData();
    });

});

