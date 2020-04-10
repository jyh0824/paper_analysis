<canvas id="compare" width="300" height="500"></canvas>
<script>
    $(function () {
        var compare = eval(<?php echo json_encode($compare); ?>);
        var year = new Array();
        var avg = new Array();
        var selection_rate = new Array();
        var judgement_rate = new Array();
        var subjective_rate = new Array();
        for (x in compare) {
            year[x] = compare[x].y;
            avg[x] = compare[x].avg;
            selection_rate[x] = compare[x].selection_rate;
            judgement_rate[x] = compare[x].judgement_rate;
            subjective_rate[x] = compare[x].subjective_rate;
        }
        var ctx = document.getElementById("compare").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: year,
                datasets: [{
                    label: '平均分',
                    data: avg,
                    borderColor: '#002c53',
                    backgroundColor: '#002c53',
                    fill: false,
                    yAxisID: 'y1',
                    pointRadius: 4,
                    pointHoverRadius: 5,
                },{
                    label: '选择题正确率',
                    data: selection_rate,
                    borderColor: '#ffa510',
                    backgroundColor: '#ffa510',
                    fill: false,
                    yAxisID: 'y2',
                    pointRadius: 4,
                    pointHoverRadius: 5,
                },{
                    label: '判断题正确率',
                    data: judgement_rate,
                    borderColor: '#0c84c6',
                    backgroundColor: '#0c84c6',
                    fill: false,
                    yAxisID: 'y2',
                    pointRadius: 4,
                    pointHoverRadius: 5,
                },{
                    label: '主观题正确率',
                    data: subjective_rate,
                    borderColor: '#41b7ac',
                    backgroundColor: '#41b7ac',
                    fill: false,
                    yAxisID: 'y2',
                    pointRadius: 4,
                    pointHoverRadius: 5,
                },]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    labels: {
                        fontSize: 17
                    },
                },
                scales: {
                    yAxes: [{
                        type: 'linear',
                        display: true,
                        id: 'y1',
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                            fontSize: 16,
                            positon: 'left',
                        },
                    }, {
                        type: 'linear',
                        display: false,
                        id: 'y2',
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                            fontSize: 16,
                            positon: 'right',
                        },
                        gridLines: {
                            drawOnChartArea: false,
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontSize: 16,
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, chart) {
                            var datasetLable = chart.datasets[tooltipItem.datasetIndex].label || '';
                            if (chart.datasets[tooltipItem.datasetIndex].yAxisID == 'y2') {
                                return datasetLable + ': ' + tooltipItem.yLabel + '%';
                            } else {
                                return datasetLable + ': ' + tooltipItem.yLabel;
                            }
                        }
                    },
                },
            }
        });
    });
</script>