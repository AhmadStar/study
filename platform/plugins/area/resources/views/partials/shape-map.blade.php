<?php
/**
 * Created by PhpStorm.
 * User: Abdulhamid
 * Date: 9/6/2025
 * Time: 11:47 PM
 */ ?>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBokt_jID9DLiGm7hbjYfVojPRUnXE-2ig&libraries=drawing&v=weekly&callback=initMap"
        async defer></script>
<div>
    <div class="mb-2" id="draw-toolbar">
        <button type="button" id="btn-poly" class="btn btn-sm btn-primary">✏️ Polygon</button>
        <button type="button" id="btn-rect" class="btn btn-sm btn-secondary">▭ Rectangle</button>
        <button type="button" id="btn-clear" class="btn btn-sm btn-light">🗑 Clear</button>
    </div>
    <div id="area-map" style="width:100%;height:420px;border-radius:12px;"></div>
    <small class="text-muted d-block mt-2">ارسم مضلعًا أو مستطيلاً. سيُحفظ الشكل كـ GeoJSON.</small>
</div>

<!-- IMPORTANT: exactly ONE Maps script tag in the page, with the drawing lib -->

<script>
    let map, drawingManager, currentOverlay = null;

    // global for callback
    window.initMap = function () {
        // quick sanity checks
        if (!window.google || !google.maps) {
            console.error('Google Maps did not load.');
            return;
        }
        if (!google.maps.drawing) {
            console.error('Drawing library missing. Check that libraries=drawing is on the ONLY script tag.');
            // We can still show the map so you see it loaded:
        }

        const mapEl = document.getElementById('area-map');
        const hidden = document.querySelector('input[name="shape"]');

        map = new google.maps.Map(mapEl, {
            center: { lat: 33.481289, lng: 36.311463 },
            zoom: 15,
            mapTypeId: 'roadmap',
        });

        // build drawing manager (controls hidden — we’ll use our buttons)
        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing ? google.maps.drawing.OverlayType.POLYGON : null, // start in polygon mode if available
            drawingControl: false, // we use custom buttons
            polygonOptions: { editable: true, draggable: false },
            rectangleOptions: { editable: true, draggable: false },
        });
        drawingManager.setMap(map);

        // if you want Google’s default toolbar instead, set drawingControl:true and remove our buttons.

        // When a shape is completed
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
            clearCurrent();
            currentOverlay = e.overlay;
            currentOverlay.type = e.type;
            writeHidden(overlayToGeoJSON(currentOverlay));

            if (e.type === 'polygon') {
                const path = currentOverlay.getPath();
                google.maps.event.addListener(path, 'set_at', () => writeHidden(overlayToGeoJSON(currentOverlay)));
                google.maps.event.addListener(path, 'insert_at', () => writeHidden(overlayToGeoJSON(currentOverlay)));
                google.maps.event.addListener(path, 'remove_at', () => writeHidden(overlayToGeoJSON(currentOverlay)));
            }
            if (e.type === 'rectangle') {
                google.maps.event.addListener(currentOverlay, 'bounds_changed', () => writeHidden(overlayToGeoJSON(currentOverlay)));
            }

            // stop drawing after first shape (click a button to re-enter)
            drawingManager.setDrawingMode(null);
        });

        // Buttons
        document.getElementById('btn-poly').onclick = () => {
            if (!google.maps.drawing) return alert('Drawing library not loaded.');
            drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
        };
        document.getElementById('btn-rect').onclick = () => {
            if (!google.maps.drawing) return alert('Drawing library not loaded.');
            drawingManager.setDrawingMode(google.maps.drawing.OverlayType.RECTANGLE);
        };
        document.getElementById('btn-clear').onclick = () => {
            clearCurrent();
            writeHidden(null);
            drawingManager.setDrawingMode(null);
        };

        // Load existing shape (if any)
        loadInitial(hidden);
    };

    function clearCurrent() {
        if (currentOverlay) {
            currentOverlay.setMap(null);
            currentOverlay = null;
        }
    }

    function overlayToGeoJSON(overlay) {
        if (overlay.type === 'rectangle' || overlay instanceof google.maps.Rectangle) {
            const b = overlay.getBounds();
            const ne = b.getNorthEast(), sw = b.getSouthWest();
            const nw = { lat: ne.lat(), lng: sw.lng() };
            const se = { lat: sw.lat(), lng: ne.lng() };
            const ring = [
                [sw.lng(), sw.lat()],
                [se.lng(), se.lat()],
                [ne.lng(), ne.lat()],
                [nw.lng(), nw.lat()],
                [sw.lng(), sw.lat()],
            ];
            return { type: 'Polygon', coordinates: [ring] };
        } else if (overlay.type === 'polygon' || overlay instanceof google.maps.Polygon) {
            const path = overlay.getPath();
            const ring = [];
            for (let i = 0; i < path.getLength(); i++) {
                const ll = path.getAt(i);
                ring.push([ll.lng(), ll.lat()]);
            }
            if (ring.length) {
                const first = ring[0], last = ring[ring.length - 1];
                if (first[0] !== last[0] || first[1] !== last[1]) ring.push(first);
            }
            return { type: 'Polygon', coordinates: [ring] };
        }
        return null;
    }

    function writeHidden(geojson) {
        const hidden = document.querySelector('input[name="shape"]');
        if (!hidden) return;
        hidden.value = geojson ? JSON.stringify({ type: 'Feature', geometry: geojson, properties: {} }) : '';
    }

    function loadInitial(hidden) {
        try {
            if (!hidden || !hidden.value) return;
            const feat = JSON.parse(hidden.value);
            if (!feat?.geometry || feat.geometry.type !== 'Polygon') return;

            const coords = feat.geometry.coordinates[0];
            const path = coords.map(([lng, lat]) => ({ lat, lng }));
            const polygon = new google.maps.Polygon({ paths: path, editable: true, map });
            polygon.type = 'polygon';
            currentOverlay = polygon;

            const bounds = new google.maps.LatLngBounds();
            path.forEach(pt => bounds.extend(pt));
            map.fitBounds(bounds);

            const polyPath = polygon.getPath();
            google.maps.event.addListener(polyPath, 'set_at', () => writeHidden(overlayToGeoJSON(polygon)));
            google.maps.event.addListener(polyPath, 'insert_at', () => writeHidden(overlayToGeoJSON(polygon)));
            google.maps.event.addListener(polyPath, 'remove_at', () => writeHidden(overlayToGeoJSON(polygon)));
            writeHidden(overlayToGeoJSON(polygon));
        } catch (e) {
            console.warn('Failed to load initial shape', e);
        }
    }
</script>


