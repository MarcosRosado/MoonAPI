
/*


################ cria um arrray de objetos ###################
function getDefaultObjectAt(array, index)
{
    return array[index] = array[index] || {};
}



getDefaultObjectAt(fullData, 1).nome = "val";  // { prop: "val" } stored at index 1.


// utilizar essa parte para gerar o array de dados,
let fullData = [];

fullData.push({"nome": "teste", "id": 1}); // insere um objeto a lista de objetos para os dados

TODO: criar um array com o tamanho len da quantidade de sensores.
TODO: recuperar a lista de de sensores pela API, agrupar os dados por sensores (foreach para lista de sensores)
TODO: pegar as listas após a leitura de todos os dados, inserir em um array de objetos no formato acima.
TODO: inserir o array de objetos na função series para gerar o gráfico.

*/
$(document).ready(function () { // gera os gráficos

setTimeout(function () {



        Highcharts.chart('tempChart', { // gera os gráficos de temperatura
            chart: {
                type: 'spline'
            },

            title: {
                text: 'Gráfico de temperatura'
            },

            subtitle: {
                text: 'Dados das ultimas 12 horas'
            },

            yAxis: {
                title: {
                    text: 'Temperatura °C'
                }
            },
            xAxis: {
                type: 'datetime',
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        tickInterval: 1,
                        enabled: true,
                    },
                }
            },

            series: fullDataTemp,

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
    }, 2000);

    setTimeout(function () {



        Highcharts.chart('umidChart', { // gera o gráfico de umidade
            chart: {
                type: 'spline'
            },

            title: {
                text: 'Gráfico de umidade'
            },

            subtitle: {
                text: 'Dados das ultimas 12 horas'
            },

            yAxis: {
                title: {
                    text: 'Umidade Analog Unit'
                }
            },
            xAxis: {
                type: 'datetime',
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        enabled: true,
                        },
                }
            },

            series: fullDataUmid,

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
    }, 2000);

});