// Detect Location & Show Map
async function detectLocation() {
    console.log("Location detection started...");

    if (!navigator.geolocation) {
        alert("Geolocation is not supported by this browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(async (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        console.log(`Latitude: ${lat}, Longitude: ${lng}`);

        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;

        // Reverse Geocoding with Nominatim
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            let address = data.display_name;

            if (data.address) {
                address = `${data.address.road || ''}, ${data.address.suburb || ''}, ${data.address.city || data.address.town || data.address.village || ''}, ${data.address.postcode || ''}`;
            }

            document.getElementById("location").value = address;
        } catch (err) {
            console.error("Reverse geocoding failed:", err);
            document.getElementById("location").value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            alert("Could not fetch address. Showing coordinates instead.");
        }

        // Show Map Container
        const mapContainer = document.getElementById("map-container");
        mapContainer.style.display = "block";

        // Initialize OpenLayers map
        const coords = ol.proj.fromLonLat([lng, lat]);

        const map = new ol.Map({
            target: 'map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            view: new ol.View({
                center: coords,
                zoom: 15
            })
        });

        // Add Marker
        const marker = new ol.Feature({
            geometry: new ol.geom.Point(coords)
        });

        marker.setStyle(new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
                src: 'https://openlayers.org/en/v4.6.5/examples/data/icon.png'
            })
        }));

        const vectorSource = new ol.source.Vector({
            features: [marker]
        });

        const markerLayer = new ol.layer.Vector({
            source: vectorSource
        });

        map.addLayer(markerLayer);

        // Ensure the map resizes correctly after becoming visible
        setTimeout(() => {
            map.updateSize();
        }, 400);

    }, () => {
        alert("Failed to fetch your location.");
    });

            // Enable clicking on map to update marker and coordinates
            map.on('click', async function (evt) {
                const clickedCoord = ol.proj.toLonLat(evt.coordinate);
                const [clickedLng, clickedLat] = clickedCoord;
    
                console.log(`Clicked at Latitude: ${clickedLat}, Longitude: ${clickedLng}`);
    
                // Update form inputs
                document.getElementById("latitude").value = clickedLat;
                document.getElementById("longitude").value = clickedLng;
    
                // Try reverse geocoding new location
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${clickedLat}&lon=${clickedLng}`);
                    const data = await response.json();
                    let address = data.display_name;
    
                    if (data.address) {
                        address = `${data.address.road || ''}, ${data.address.suburb || ''}, ${data.address.city || data.address.town || data.address.village || ''}, ${data.address.postcode || ''}`;
                    }
    
                    document.getElementById("location").value = address;
                } catch (err) {
                    console.error("Reverse geocoding failed:", err);
                    document.getElementById("location").value = `${clickedLat.toFixed(6)}, ${clickedLng.toFixed(6)}`;
                }
    
                // Move marker to new location
                marker.setGeometry(new ol.geom.Point(ol.proj.fromLonLat([clickedLng, clickedLat])));
            });
    
}

// Handle Form Submission via Fetch API
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
        document.getElementById("location").value = "";
    })
    .catch(err => {
        console.error("Error submitting form:", err);
        alert("Failed to submit the form.");
    });
});
