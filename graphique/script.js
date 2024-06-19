document.addEventListener("DOMContentLoaded", function () {
    const temperatureChart1 = createChart('temperatureChart1', 'Température ESP32_1 (°C)', 50);
    const temperatureChart2 = createChart('temperatureChart2', 'Température ESP32_2 (°C)', 50);

    updateChart('temperature', 'hour', 'esp32_1');
    updateChart('temperature', 'hour', 'esp32_2');

    function createChart(canvasId, label, maxValue) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: label,
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxValue
                    }
                }
            }
        });
    }

    function updateChart(chartType, period, deviceId) {
        fetch(`data.php?type=${chartType}&period=${period}&device_id=${deviceId}`)
            .then(response => response.json())
            .then(data => {
                let chart;
                switch(deviceId) {
                    case 'esp32_1':
                        chart = temperatureChart1;
                        break;
                    case 'esp32_2':
                        chart = temperatureChart2;
                        break;
                }
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.values;
                chart.update();
            })
            .catch(error => console.error('Erreur lors de la récupération des données:', error));
    }

    window.updateChart = updateChart; 
});
