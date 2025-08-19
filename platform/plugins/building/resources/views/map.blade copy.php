@extends('core/base::layouts.master')

@section('content')
    <div id="map" style="width: 100%; height: 600px;"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let map = L.map('map', {
                maxZoom: 22
            }).setView([33.4750, 36.3090], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.tileLayer(
                'https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}?access_token=YOUR_MAPBOX_ACCESS_TOKEN', {
                    tileSize: 512,
                    zoomOffset: -1,
                    attribution: '© Mapbox © OpenStreetMap',
                    maxZoom: 22
                }).addTo(map);

            @foreach ($buildings as $building)
                {
                    const marker = L.marker([{{ $building->latitude }}, {{ $building->longitude }}]).addTo(map);
                    marker.bindPopup(
                        `<b>{{ $building->name }}</b><br><button onclick="loadResidents({{ $building->id }})">View Residents</button>`
                        );
                }
            @endforeach
        });

        function loadResidents(buildingId) {
            fetch(`/building/${buildingId}/residents`)
                .then(res => res.json())
                .then(data => {
                    let html = '<ul>';
                    data.forEach(person => {
                        html += `<li><a href="/person/${person.id}">${person.name}</a></li>`;
                    });
                    html += '</ul>';
                    L.popup()
                        .setLatLng([0, 0]) // position not used, just display popup
                        .setContent(html)
                        .openOn(map);
                });
        }
    </script>
@endsection
