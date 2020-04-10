<canvas id="segment" width="500" height="500"></canvas>
<script>
    $(function () {
        var ctx = document.getElementById("segment").getContext('2d');
        var data = eval(<?php echo json_encode($segment); ?>);
        var labels = new Array();
        labels[0] = '优秀:'+data.a;
        labels[1] = '良好:'+data.b;
        labels[2] = '中等:'+data.c;
        labels[3] = '及格:'+data.d;
        labels[4] = '不及格:'+data.e;
        var datas = new Array();
        datas[0] = data.a;
        datas[1] = data.b;
        datas[2] = data.c;
        datas[3] = data.d;
        datas[4] = data.e;
        var color = new Array();
        for (var i=0; i<5; i++) {
            if (i != data.your) {
                color[i] = 'darkgrey';
            } else {
                color[i] = '#ffa510';
            }
        }
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '成绩段',
                    data: datas,
                    backgroundColor: color,
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    labels: {
                        fontSize: 17
                    }
                },
                scales: {
                    yAxes: {
                        ticks: {
                            setpSize: 1,
                        }
                    }
                }
            }
        });
    });
</script>