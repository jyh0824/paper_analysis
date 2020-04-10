<canvas id="segment" width="500" height="500"></canvas>
<script>
    $(function () {
        var data = eval(<?php echo json_encode($segment); ?>);
        var labels = new Array();
        labels[0] = '优秀:'+data[0];
        labels[1] = '良好:'+data[1];
        labels[2] = '中等:'+data[2];
        labels[3] = '及格:'+data[3];
        labels[4] = '不及格:'+data[4];
        var ctx = document.getElementById("segment").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: [
                        '优秀',
                        '良好',
                        '中等',
                        '及格',
                        '不及格'
                    ],
                    data: [
                        {{ $segment[0] }},
                        {{ $segment[1] }},
                        {{ $segment[2] }},
                        {{ $segment[3] }},
                        {{ $segment[4] }}
                    ],
                    backgroundColor: [
                        '#002c53',
                        '#ffa510',
                        '#0c84c6',
                        '#41b7ac',
                        '#f74d4d',
                    ],
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    labels: {
                        fontSize: 17
                    }
                }
            }
        });
    });
</script>