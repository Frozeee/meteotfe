document.addEventListener("DOMContentLoaded", function () {
    fetch('send.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('temp-ext').innerHTML = `${data.tempExt.toFixed(1)}&deg;C`;
            document.getElementById('hum-ext').textContent = `${data.humExt.toFixed(1)}%`;
            document.getElementById('light').textContent = formatLight(data.light.toFixed(1));
            document.getElementById('temp-int').innerHTML = `${data.tempInt.toFixed(1)}&deg;C`;
            document.getElementById('hum-int').textContent = `${data.humInt.toFixed(1)}%`;
            document.getElementById('voc_index').textContent = `${data.vocIndex.toFixed(1)} cov`;
        });
});

function formatLight(lux) {
    let lightValue = lux * 10;

    if (lightValue >= 1000) {
        return (lightValue / 1000).toFixed(1) + 'k lux';
    }
    
    return lightValue.toFixed(1) + ' lux';
}
