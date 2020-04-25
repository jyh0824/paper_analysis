<canvas id="rate" width="500" height="500"></canvas>
<script>
    $(function () {
        var ctx = document.getElementById("rate").getContext('2d');
        var data = eval(<?php echo json_encode($rate); ?>);
        var labels = new Array();
        var datas = new Array();
        if (typeof(data.judgement_score) == 'undefined') {
            labels[0] = '选择题';
            labels[1] = '主观题';
            datas[0] = data.selection_score;
            datas[1] = data.subjective_score;
        } else {
            labels[0] = '选择题';
            labels[1] = '判断题';
            labels[2] = '主观题';
            datas[0] = data.selection_score;
            datas[1] = data.judgement_score;
            datas[2] = data.subjective_score;
        }
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '得分率',
                    data: datas,
                    backgroundColor: [
                        '#002c53',
                        '#ffa510',
                        '#0c84c6',
                    ],
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    labels: {
                        fontSize: 17
                    },
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (label, index, labels) {
                                return Math.floor((label)) + '%';
                            },
                            max: 100,
                            fontSize: 16,
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
                            return datasetLable + ':' + tooltipItem.yLabel + '%';
                        }
                    }
                },
            }
        });
    });
</script>