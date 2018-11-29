<canvas id="myChart" width="200" height="80"></canvas>
<script>
$.get('get_chart_data',function (data, status) {
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["已认证教练", "未认证教练", "已录入学员", "未录入学员", "总教练", "总学员"],
            datasets: [{
                label: '# 数量',
                data: [
                    data.checked_trainer, data.checking_trainer, data.checked_student, data.checking_student, data.all_trainer, data.all_student
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
                    responsive: true,
                }
    });
});
</script>