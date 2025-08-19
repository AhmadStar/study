@extends('core/base::layouts.master')

@section('content')

    <style>
        /* Modal overlay */
        #residentsModalOverlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5);
            display: none; /* hidden initially */
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        /* Modal container */
        #residentsModal {
            background: white;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 8px;
            padding: 20px;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: relative;
        }
        /* Close button */
        #residentsModalClose {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            color: #888;
            cursor: pointer;
            user-select: none;
        }
        #residentsModalClose:hover {
            color: #333;
        }
        /* Residents list styling */
        #residentsModal ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #residentsModal li {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        #residentsModal img.avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
        }
        #residentsModal .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ccc;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            margin-right: 12px;
        }
        #residentsModal a {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
        }
        #residentsModal a:hover {
            text-decoration: underline;
        }
    </style>

    <div id="map" style="width: 100%; height: 600px;"></div>

    <!-- Modal HTML -->
    <div id="residentsModalOverlay">
        <div id="residentsModal">
            <span id="residentsModalClose">&times;</span>
            <div id="residentsModalContent"></div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBokt_jID9DLiGm7hbjYfVojPRUnXE-2ig"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const center = { lat: 33.4750 + 0.0066, lng: 36.3090 };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 17,
                center: center,
                mapTypeId: 'satellite'
            });

            // Modal elements
            const modalOverlay = document.getElementById('residentsModalOverlay');
            const modalContent = document.getElementById('residentsModalContent');
            const modalClose = document.getElementById('residentsModalClose');

            modalClose.addEventListener('click', () => {
                modalOverlay.style.display = 'none';
                modalContent.innerHTML = '';
            });

            // Close modal on clicking outside modal box
            modalOverlay.addEventListener('click', e => {
                if (e.target === modalOverlay) {
                    modalOverlay.style.display = 'none';
                    modalContent.innerHTML = '';
                }
            });

            const statusColors = {
                'danger': '#FF0000',       // Bright Red
                'high_risk': '#FF4500',    // Orange Red
                'moderate': '#FFD700',     // Bright Yellow
                'safe': '#00FF00',         // Bright Green
                'evacuated': '#0000FF'     // Strong Blue
            };

            @foreach ($buildings as $building)
                // Random status and color for demo
                 statusKeys = Object.keys(statusColors);
                 randomStatusKey = statusKeys[Math.floor(Math.random() * statusKeys.length)];
                 color = statusColors[randomStatusKey] || '#808080';

                 circle{{ $building->id }} = new google.maps.Circle({
                    strokeColor: color,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: color,
                    fillOpacity: 0.6,
                    map: map,
                    center: { lat: {{ $building->latitude }}, lng: {{ $building->longitude }} },
                    radius: 6
                });

                // Pulse animation for danger (red) status circles
                if (color === '#FF0000') {
                    let growing = true;
                    setInterval(() => {
                        let r = circle{{ $building->id }}.getRadius();
                        if (growing) {
                            r += 0.5;
                            if (r >= 10) growing = false;
                        } else {
                            r -= 0.5;
                            if (r <= 6) growing = true;
                        }
                        circle{{ $building->id }}.setRadius(r);
                    }, 50);
                }

                // Click handler to open modal with residents list
                circle{{ $building->id }}.addListener('click', function() {
                    modalContent.innerHTML = `<p>Loading residents for <strong>{{ $building->name }}</strong>...</p>`;
                    modalOverlay.style.display = 'flex';

                    fetch(`/building/{{ $building->id }}/residents`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error(`HTTP error! Status: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data.length) {
                                modalContent.innerHTML = '<p>No residents found.</p>';
                                return;
                            }

                            let html = `<h3>{{ $building->name }}</h3><ul>`;

                            data.forEach(person => {
                                const displayName = person.full_name || 'Unknown';
                                const avatar = person.avatar
                                    ? `<img class="avatar" src="${person.avatar}" alt="${displayName}">`
                                    : `<div class="avatar-placeholder">${displayName.charAt(0)}</div>`;

                                html += `
                                    <li>
                                        ${avatar}
                                        <a href="/person/${person.id}" target="_blank">${displayName}</a>
                                    </li>
                                `;
                            });

                            html += '</ul>';
                            modalContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            modalContent.innerHTML = '<p>Failed to load residents.</p>';
                        });
                });
            @endforeach
        });
    </script>

@endsection
