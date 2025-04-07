function detectLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            showPosition,
            () => alert("Geolocation failed."),
            { enableHighAccuracy: true }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;

    document.getElementById("latitude").value = lat;
    document.getElementById("longitude").value = lon;

    document.getElementById("map-container").style.display = "block";

    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([lon, lat]),
            zoom: 15
        })
    });

    const marker = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat]))
    });

    const vectorSource = new ol.source.Vector({
        features: [marker]
    });

    const markerVectorLayer = new ol.layer.Vector({
        source: vectorSource
    });

    map.addLayer(markerVectorLayer);
}

// Handle form submission
document.getElementById("reportForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("api/report_issue.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        alert("Response: " + data);
        this.reset();
        document.getElementById("map-container").style.display = "none";
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Failed to submit the form.");
    });
});
