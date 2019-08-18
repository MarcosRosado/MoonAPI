
/*
let umidade1, umidade2, umidade3, umidade4, umidade5, umidade6, umidade7, umidade8, umidade9, umidade10,
    umidade11, umidade12, umidade13, umidade14, umidade15, umidade16, umidade17, umidade18, umidade19, umidade20;
let temperatura1, temperatura2, temperatura3, temperatura4, temperatura5, temperatura6, temperatura7, temperatura8,
    temperatura9, temperatura10, temperatura11, temperatura12, temperatura13, temperatura14, temperatura15, temperatura16,
    temperatura17, temperatura18, temperatura19, temperatura;

################ cria um arrray de objetos ###################
function getDefaultObjectAt(array, index)
{
    return array[index] = array[index] || {};
}



getDefaultObjectAt(fullData, 1).nome = "val";  // { prop: "val" } stored at index 1.

let fullData = [];

fullData.push({"nome": "teste", "id": 1}); // insere um objeto a lista de objetos para os dados

TODO: recuperar a lista de de sensores pela API, agrupar os dados por sensores (foreach para lista de sensores)
TODO: pegar as listas após a leitura de todos os dados, inserir em um array de objetos no formato acima.
TODO: inserir o array de objetos na função series para gerar o gráfico.

*/

// recebe o timestamp atual para comparar com o timestamp da leitura
let someDate = new Date();
let curTimestamp = Math.floor(someDate.getTime()/1000); // pega o epoch atual

// moldes de tempo para exibir no gráfico
let h12 = 43200; // tempo em horas de meio dia‬
let m20 = 1200;

// recupera os valores retornados da API e preenche os Labels e Data do gráfico
for (let i=0; i< DataArray.length; i++){
    if (parseInt(DataArray[i][2]) + h12 >= curTimestamp) {
        if (DataArray[i][1] === "Umid") { // exibe os dados de temperatura
            let d = new Date(parseInt(DataArray[i][2]) * 1000);
            d.toLocaleString();
            let hours = d.getHours();
            let mins = d.getMinutes();
            let day = d.getDate();
            let month = d.getMonth() + 1;
            let returnString = day + "/" + month + " " + hours + ":" + mins;


            DataChart.push([returnString , parseFloat(DataArray[i][0])]);

            LabelChart.push(returnString);
            Label = DataArray[i][3];
        }
    }
}


Highcharts.chart('highchart', {

    title: {
        text: 'Solar Employment Growth by Sector, 2010-2016'
    },

    subtitle: {
        text: 'Source: thesolarfoundation.com'
    },

    yAxis: {
        title: {
            text: 'Number of Employees'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 2010
        }
    },

    series: [{
        name: 'Installation',
        data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
    }, {
        name: 'Manufacturing',
        data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
    }, {
        name: 'Sales & Distribution',
        data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
    }, {
        name: 'Project Development',
        data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
    }, {
        name: 'Other',
        data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});