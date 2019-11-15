
$(document).ready(function () { // gera os gráficos

    // recebe qual a data que o gráfico está trabalhando
    let d = new Date(datesearch*1000);
    d.toLocaleString();
    let year = d.getFullYear();
    let day = d.getDate();
    let month = d.getMonth() + 1;
    let dataGrafico = day+"/"+month+"/"+year;

setTimeout(function () {



        Highcharts.chart('tempChart', { // gera os gráficos de temperatura
            chart: {
                type: 'spline',
                zoomType: 'xy'
            },

            title: {
                text: 'Gráfico de temperatura'
            },

            subtitle: {
                text: dataGrafico
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
    }, 8000);

    setTimeout(function () {



        Highcharts.chart('umidChart', { // gera o gráfico de umidade
            chart: {
                type: 'spline',
                zoomType: 'xy'
            },

            title: {
                text: 'Gráfico de umidade'
            },

            subtitle: {
                text: dataGrafico
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
    }, 12000);

});