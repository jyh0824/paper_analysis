<canvas id="rate" width="300" height="500"></canvas>
<script>
    $(function () {
        var ctx = document.getElementById("rate").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    '选择题',
                    '判断题',
                    '主观题'
                ],
                datasets: [{
                    label: '得分率',
                    data: [
                        {{ $rate[0] }},
                        {{ $rate[1] }},
                        {{ $rate[2] }},
                    ],
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