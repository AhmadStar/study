/**
 * Created by Abdulhamid on 12/5/2024.
 * Enhanced map + create-building modal using Vue 2 and Google Maps JS API.
 *
 * Requirements in the Blade:
 * - <div id="mapBuilding"><div id="map"></div> + existing residents modal markup (as you already have)
 * - Scripts loaded (in this order): Google Maps API, Vue 2, THIS file
 *
 * Optional: Provide buildings/areas from Blade:
 *   <script>
 *     window.BUILDINGS = {!! $buildings->toJson() !!}; // each: {id,name,latitude,longitude}
 *   </script>
 */

(function () {
    // ---- CONFIG (adjust endpoints if your routes differ) ----
    var CONFIG = {
        MAP_CENTER: { lat: 33.475760, lng: 36.317152 },
        MAP_ZOOM: 17,
        MAP_TYPE_ID: 'satellite',

        AREAS_GET_URL: (window.ROUTES && window.ROUTES.areas) || '/building/areas',
        RESIDENTS_GET_URL: function (buildingId) {
            var base = (window.ROUTES && window.ROUTES.residentsBase) || '/building-info';
            return base + '/' + buildingId + '';
        },
        BUILDINGS_GET_URL: '/building/list',
        BUILDINGS_CREATE_URL: (window.ROUTES && window.ROUTES.create) || '/buildings/from-map',

        // RESIDENTS_GET_URL: function (buildingId) {
        //     var base = (window.ROUTES && window.ROUTES.infoBase) || '/building/';
        //     return base + buildingId + '/info';
        // },
        BUILDING_ADD_URL: function (id) {
            var base = (window.ROUTES && window.ROUTES.buildingEditBase) || '/admin/buildings';
            return base + '/add';
        },
        BUILDING_EDIT_URL: function (id) {
            var base = (window.ROUTES && window.ROUTES.buildingEditBase) || '/admin/buildings';
            return base + '/' + id + '/edit';
        },
        FAMILY_EDIT_URL: function (id) {
            var base = (window.ROUTES && window.ROUTES.familyEditBase) || '/admin/families';
            return base + '/' + id + '/edit';
        },
        FAMILY_ADD_URL: function (buildingId) {
            var base = (window.ROUTES && window.ROUTES.familyAdd) || '/admin/families';
            return base + '/' + buildingId + '/add';
        },
        FAMILY_DELETE_URL: function (id) {
            var base = (window.ROUTES && window.ROUTES.familyDeleteBase) || '/admin/families';
            return base + '/' + id + '/delete';
        },
    };

    var familyCloseBtn = document.getElementById('familyCreateModalClose');
    if (familyCloseBtn) {
        familyCloseBtn.addEventListener('click', function () {
            if (adminApp) {
                adminApp.openFamilyCreateModal = 0;   // يخفي المودال (لو مستخدم v-if / v-show في Vue)
            }
            var overlay = document.getElementById('familyCreateModalOverlay');
            if (overlay) overlay.style.display = 'none'; // إغلاق الواجهة تماماً
        });
    }


    // ---- UTIL ----
    function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
    function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }
    function getCSRFToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : null;
    }
    function jsonSafe(res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }
    function normalizeArray(payload) {
        if (Array.isArray(payload)) return payload;
        if (payload && Array.isArray(payload.data)) return payload.data;
        return [];
    }

    // ---- VUE APP ----
    var adminApp = new Vue({
        el: '#mapBuilding',

        data: {
            // map + data
            map: null,
            openFamilyCreateModal: 0,
            buildingCreated: null,
            circles: [],
            statusColors: {
                danger: '#FF0000',
                high_risk: '#FF4500',
                moderate: '#FFD700',
                safe: '#00FF00',
                evacuated: '#0000FF',
            },

            // residents modal elements (already in your Blade)
            modalOverlay: null,
            modalContent: null,
            modalClose: null,

            // create-building UI
            createBtn: null,
            createOverlay: null,
            createContent: null,
            createClose: null,
            saving: false,
            successMsg: null,
            errorMsg: null,

            // form state
            form: {
                name: '',
                latitude: '',
                longitude: '',
                address: '',
                description: '',
                area_id: '',
                building_number: '',
                floors_count: null,
            },
            areas: [],
            createMode: false, // if true, first click on map opens the form with coords

            // data sources
            buildings: [],
        },

        mounted: function () {
            var self = this;
            // self.$nextTick(function() {
            //     var familyCloseBtn = document.getElementById('familyCreateModalClose');
            //     if (familyCloseBtn) {
            //         familyCloseBtn.addEventListener('click', function () {
            //             self.openFamilyCreateModal = 0;
            //             var overlay = document.getElementById('familyCreateModalOverlay');
            //             if (overlay) overlay.style.display = 'none';
            //             var content = document.getElementById('familyCreateModalContent');
            //             if (content) content.innerHTML = '';
            //         });
            //     }
            // });



            // Prepare residents modal hooks (from existing Blade)
            self.modalOverlay = qs('#residentsModalOverlay');
            self.modalContent = qs('#residentsModalContent');
            self.modalClose = qs('#residentsModalClose');
            self.areas = window.areas;
            if (self.modalClose) {
                self.modalClose.addEventListener('click', function () {
                    self.modalOverlay.style.display = 'none';
                    self.modalContent.innerHTML = '';
                });
            }
            if (self.modalOverlay) {
                self.modalOverlay.addEventListener('click', function (e) {
                    if (e.target === self.modalOverlay) {
                        self.modalOverlay.style.display = 'none';
                        self.modalContent.innerHTML = '';
                    }
                });
            }

            // Inject Create button (if not in Blade already)
            self.injectCreateButton();

            // Inject Create modal (overlay)
            self.injectCreateModal();

            // Init map
            self.initMap();

            // Load areas list for <select>
            self.loadAreas();

            // Load buildings (from window or via fetch)
            self.loadBuildings().then(function () {
                self.drawBuildings();
            });
        },

        methods: {
            // ---------- UI INJECTION ----------
            closeModel:function () {
                var familyCloseBtn = document.getElementById('familyCreateModalClose');
                if (familyCloseBtn) {
                    familyCloseBtn.addEventListener('click', function () {
                        self.openFamilyCreateModal = 0;
                        var overlay = document.getElementById('familyCreateModalOverlay');
                        if (overlay) overlay.style.display = 'none';
                        var content = document.getElementById('familyCreateModalContent');
                        if (content) content.innerHTML = '';
                    });
                }
            },
            injectCreateButton: function () {
                var self = this;
                // Add a simple top-right button overlay on the map container
                var host = qs('#mapBuilding');
                if (!host) return;

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.id = 'createBuildingBtn';
                btn.textContent = 'إضافة بناء جديد';
                Object.assign(btn.style, {
                    position: 'absolute',
                    top: '12px',
                    right: '12px',
                    zIndex: 10001,
                    background: '#0d6efd',
                    color: '#fff',
                    border: 'none',
                    padding: '10px 14px',
                    borderRadius: '6px',
                    cursor: 'pointer',
                    boxShadow: '0 2px 8px rgba(0,0,0,.25)',
                });

                host.style.position = 'relative';
                host.appendChild(btn);
                self.createBtn = btn;

                btn.addEventListener('click', function () {
                    // Enable "create mode": the next map click opens the form with coords.
                    self.createMode = true;
                    self.toast('Click on the map to choose a location…');
                });
            },

            injectCreateModal: function () {
                var self = this;

                // Overlay
                var overlay = document.createElement('div');
                overlay.id = 'createBuildingModalOverlay';
                Object.assign(overlay.style, {
                    position: 'fixed',
                    top: 0, left: 0,
                    width: '100vw', height: '100vh',
                    background: 'rgba(0,0,0,.5)',
                    display: 'none',
                    justifyContent: 'center',
                    alignItems: 'center',
                    zIndex: 10002
                });

                // Modal
                var modal = document.createElement('div');
                modal.id = 'createBuildingModal';
                Object.assign(modal.style, {
                    background: '#fff',
                    width: '90%', maxWidth: '700px',
                    maxHeight: '80vh', overflowY: 'auto',
                    borderRadius: '8px', padding: '20px',
                    fontFamily: 'Arial, sans-serif',
                    position: 'relative',
                    boxShadow: '0 2px 10px rgba(0,0,0,.3)'
                });

                // Close
                var close = document.createElement('span');
                close.id = 'createBuildingModalClose';
                close.textContent = '×';
                Object.assign(close.style, {
                    position: 'absolute',
                    top: '10px', right: '15px',
                    fontSize: '28px', color: '#888',
                    cursor: 'pointer', userSelect: 'none'
                });
                close.addEventListener('mouseenter', function () { close.style.color = '#333'; });
                close.addEventListener('mouseleave', function () { close.style.color = '#888'; });
                close.addEventListener('click', this.closeCreateModal);

                // Content (Vue will bind via template literals)
                var content = document.createElement('div');
                content.id = 'createBuildingModalContent';

                modal.appendChild(close);
                modal.appendChild(content);
                overlay.appendChild(modal);
                document.body.appendChild(overlay);

                this.createOverlay = overlay;
                this.createContent = content;

                // Render initial form content
                this.renderCreateForm();
            },

            renderCreateForm: function () {
                // Build the form HTML bound via simple ids; Vue will sync values in code (not v-model in external DOM)
                var html = `
  <h3 style="margin-top:0">إنشاء مبنى جديد</h3>
  <div id="createBuildingAlert" style="display:none;margin-bottom:10px;"></div>
  <form id="createBuildingForm">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label>الاسم</label>
        <input id="f_name" type="text" class="form-control" style="width:100%" required>
      </div>
      <div>
        <label>المنطقة</label>
        <select id="f_area_id" class="form-select" style="width:100%" required>
          <option value="">اختر المنطقة…</option>
        </select>
      </div>
      <div>
        <label>خط العرض</label>
        <input id="f_latitude" type="text" class="form-control" style="width:100%" readonly>
      </div>
      <div>
        <label>خط الطول</label>
        <input id="f_longitude" type="text" class="form-control" style="width:100%" readonly>
      </div>
      <div style="grid-column:1 / span 2">
        <label>العنوان</label>
        <input id="f_address" type="text" class="form-control" style="width:100%">
      </div>
      <div>
        <label>رقم المبنى</label>
        <input id="f_building_number" type="text" class="form-control" style="width:100%">
      </div>
      <div>
        <label>عدد الطوابق</label>
        <input id="f_floors_count" type="number" min="0" class="form-control" style="width:100%">
      </div>
      <div style="grid-column:1 / span 2">
        <label>الوصف</label>
        <textarea id="f_description" rows="3" class="form-control" style="width:100%"></textarea>
      </div>
    </div>
    <div style="margin-top:16px;text-align:right">
      <button type="button" id="btnCancelCreate" class="btn btn-secondary" style="margin-right:8px">إلغاء</button>
      <button type="submit" id="btnSaveCreate" class="btn btn-primary">حفظ</button>
    </div>
  </form>
`;

                this.createContent.innerHTML = html;

                // Wire events
                var self = this;
                qs('#btnCancelCreate', this.createContent).addEventListener('click', self.closeCreateModal);
                qs('#createBuildingForm', this.createContent).addEventListener('submit', function (e) {
                    e.preventDefault();
                    self.saveBuilding();
                });

                // Populate areas
                var sel = qs('#f_area_id', this.createContent);
                sel.innerHTML = '<option value="">اختر المنطقة…</option>' +
                    this.areas.map(function (a) {
                        return '<option value="' + a.id + '">' + (a.name || ('#' + a.id)) + '</option>';
                    }).join('');
            },

            openCreateModal: function () {
                if (this.createOverlay) this.createOverlay.style.display = 'flex';
                // sync inputs with current form values
                if (this.createContent) {
                    var F = this.form;
                    var m = this.createContent;
                    var setVal = function (id, val) { var el = qs(id, m); if (el) el.value = (val == null ? '' : String(val)); };
                    setVal('#f_name', F.name);
                    setVal('#f_latitude', F.latitude);
                    setVal('#f_longitude', F.longitude);
                    setVal('#f_address', F.address);
                    setVal('#f_description', F.description);
                    setVal('#f_building_number', F.building_number);
                    setVal('#f_floors_count', F.floors_count);
                    var sel = qs('#f_area_id', m);
                    if (sel) sel.value = F.area_id || '';
                }
            },

            closeCreateModal: function () {
                if (this.createOverlay) this.createOverlay.style.display = 'none';
                this.errorMsg = null;
            },

            toast: function (text) {
                // minimal inline toast using successMsg area
                this.successMsg = text;
                var self = this;
                setTimeout(function () { self.successMsg = null; }, 1800);
            },

            // ---------- DATA LOAD ----------
            loadAreas: function () {
                var self = this;
                return fetch(CONFIG.AREAS_GET_URL, { headers: { 'Accept': 'application/json' } })
                    .then(jsonSafe)
                    .then(function (data) {
                        self.areas = normalizeArray(data);
                        // refresh select options if modal already built
                        if (self.createContent) self.renderCreateForm();
                    })
                    .catch(function () {
                        // ignore silently if endpoint not available
                    });
            },

            loadBuildings: function () {
                var self = this;
                if (Array.isArray(window.BUILDINGS) && window.BUILDINGS.length) {
                    self.buildings = window.BUILDINGS;
                    return Promise.resolve();
                }
                // fallback to fetch
                return fetch(CONFIG.BUILDINGS_GET_URL, { headers: { 'Accept': 'application/json' } })
                    .then(jsonSafe)
                    .then(function (data) {
                        self.buildings = normalizeArray(data);
                    })
                    .catch(function () {
                        self.buildings = [];
                    });
            },

            // ---------- MAP ----------
            initMap: function () {
                this.map = new google.maps.Map(document.getElementById('map'), {
                    zoom: CONFIG.MAP_ZOOM,
                    center: CONFIG.MAP_CENTER,
                    mapTypeId: CONFIG.MAP_TYPE_ID
                });

                // click to fill lat/lng (and open modal if createMode)
                this.map.addListener('click', this.onMapClick);
            },

            drawBuildings: function () {
                var self = this;
                // Clear existing circles
                self.circles.forEach(function (c) { c.circle.setMap(null); });
                self.circles = [];

                var statusKeys = Object.keys(self.statusColors);

                self.buildings.forEach(function (b) {
                    var statusKey = statusKeys[Math.floor(Math.random() * statusKeys.length)];
                    var color = self.statusColors[statusKey] || '#808080';

                    var circle = new google.maps.Circle({
                        strokeColor: color,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: color,
                        fillOpacity: 0.6,
                        map: self.map,
                        center: { lat: Number(b.latitude), lng: Number(b.longitude) },
                        radius: 6
                    });

                    // pulse animate if red
                    if (color === '#FF0000') {
                        var growing = true;
                        setInterval(function () {
                            var r = circle.getRadius();
                            if (growing) {
                                r += 0.5; if (r >= 10) growing = false;
                            } else {
                                r -= 0.5; if (r <= 6) growing = true;
                            }
                            circle.setRadius(r);
                        }, 50);
                    }

                    // click => open residents modal
                    circle.addListener('click', function () {
                        if (!self.modalOverlay || !self.modalContent) return;
                        self.modalContent.innerHTML = '<p>Loading residents for <strong>' + (b.name || ('#' + b.id)) + '</strong>...</p>';
                        self.modalOverlay.style.display = 'flex';

                        fetch(CONFIG.RESIDENTS_GET_URL(b.id), { headers: { 'Accept': 'application/json' } })
                            .then(jsonSafe)
                            .then(function (resp) {
                                if (resp.error) {
                                    self.modalContent.innerHTML = '<p>Failed to load building info.</p>';
                                    return;
                                }

                                var building = resp.data || {};
                                var families = Array.isArray(building.families) ? building.families : [];

                                // Local renderer so we can re-render after deletes
function render() {
    var header = `
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
  <div>
    <h3 style="margin:0">${building.name || ('#' + building.id)}</h3>
    <div style="margin-top:6px;font-size:13px;color:#555">
      <span style="display:inline-block;background:#f1f3f5;border:1px solid #e9ecef;border-radius:12px;padding:4px 8px;margin-right:6px">
        <strong>رقم المبنى:</strong> ${building.building_number || '—'}
      </span>
      <span style="display:inline-block;background:#f1f3f5;border:1px solid #e9ecef;border-radius:12px;padding:4px 8px;margin-right:6px">
        <strong>عدد الطوابق:</strong> ${building.floors_count ?? '—'}
      </span>
      <span style="display:inline-block;background:#f1f3f5;border:1px solid #e9ecef;border-radius:12px;padding:4px 8px">
        <strong>عدد العائلات:</strong> ${families.length}
      </span>
    </div>
    <div style="margin-top:8px;color:#333"><strong>العنوان:</strong> ${building.address || '—'}</div>
  </div>

  <div style="display:flex;gap:8px;flex-shrink:0">
    <button type="button" class="btn btn-primary" data-action="add-family" data-building-id="${building.id}" style="padding:8px 12px;border-radius:6px">+ إضافة عائلة</button>
    <button type="button" class="btn btn-secondary" data-action="edit-building" style="padding:8px 12px;border-radius:6px">تعديل المبنى</button>
  </div>
</div>
<hr style="margin:12px 0">
`;

    var list;
    if (!families.length) {
        list = `
  <div style="background:#fff3cd;border:1px solid #ffeeba;color:#856404;border-radius:6px;padding:10px 12px;margin:8px 0">
    لا توجد عائلات في هذا المبنى.
  </div>
`;
    } else {
        list = `
  <div>
    <h4 style="margin:0 0 8px">العائلات</h4>
    <ul style="list-style:none;padding:0;margin:0">
      ${families.map(function (family) {
            var members = (family.count_family_members != null) ? family.count_family_members : '—';
            var houseType = family.house_type ? `<span style="display:inline-block;background:#eef7ff;border:1px solid #d2e7ff;border-radius:12px;padding:2px 8px;margin-left:6px;font-size:12px">${family.house_type}</span>` : '';
            return `
        <li style="border:1px solid #e9ecef;border-radius:10px;padding:10px 12px;margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
            <div style="min-width:0">
              <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                ${family.family_name || 'عائلة بدون اسم'} ${houseType}
              </div>
              <div style="font-size:13px;color:#555;margin-top:3px">
                <span style="margin-right:10px"><strong>عدد الأفراد:</strong> ${members}</span>
                ${family.family_code ? `<span style="margin-right:10px"><strong>الرمز:</strong> ${family.family_code}</span>` : ''}
                ${family.floor_number != null ? `<span style="margin-right:10px"><strong>الطابق:</strong> ${family.floor_number}</span>` : ''}
              </div>
              ${family.address ? `<div style="font-size:13px;color:#666;margin-top:3px">${family.address}</div>` : ''}
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0">
              <button type="button" class="btn btn-light" data-action="edit-family" data-family-id="${family.id}" style="padding:6px 10px;border:1px solid #ced4da;border-radius:6px">تعديل</button>
              <button type="button" class="btn btn-danger" data-action="delete-family" data-family-id="${family.id}" style="padding:6px 10px;border-radius:6px">حذف</button>
            </div>
          </div>
          ${family.notes ? `<div style="margin-top:8px;font-size:13px;color:#495057;background:#f8f9fa;border:1px dashed #dee2e6;border-radius:6px;padding:8px">${family.notes}</div>` : ''}
        </li>
      `;
        }).join('')}
    </ul>
  </div>
`;
    }

    self.modalContent.innerHTML = header + list;
}


                                render(); // initial render

                                // Button actions (event delegation)
                                self.modalContent.onclick = function (e) {
                                    var btn = e.target.closest('button[data-action]');
                                    if (!btn) return;

                                    var action = btn.getAttribute('data-action');

                                    if (action === 'add-family') {
                                        var bid = btn.getAttribute('data-building-id');
                                        if (bid) {
                                            self.openFamilyCreateModal = 1;

                                            // ابحث عن البناء داخل this.buildings
                                            self.buildingCreated = (self.buildings || []).find(function (b) {
                                                return String(b.id) === String(bid);
                                            });

                                            console.log('building created:', self.buildingCreated);

                                            // 🔴 إخفاء نافذة السكان
                                            if (self.modalOverlay) {
                                                self.modalOverlay.style.display = 'none';
                                                self.modalContent.innerHTML = '';
                                            }
                                        }

                                        // Reset + close
                                        setTimeout(function () {
                                            self.closeCreateModal();
                                            self.clearCreateAlert();
                                        }, 800);

                                        self.resetFormKeepCoords();
                                        // if (bid) window.location.href = CONFIG.FAMILY_ADD_URL(bid);
                                    }


                                    if (action === 'edit-building') {
                                        window.location.href = CONFIG.BUILDING_EDIT_URL(building.id);
                                    }

                                    if (action === 'edit-family') {
                                        var fid = btn.getAttribute('data-family-id');
                                        if (fid) window.location.href = CONFIG.FAMILY_EDIT_URL(fid);
                                    }

                                    if (action === 'add-family') {
                                        var bid = btn.getAttribute('data-building-id');
                                        if (bid) {
                                            self.openFamilyCreateModal = 1;

                                            // ابحث عن البناء داخل this.buildings
                                            self.buildingCreated = (self.buildings || []).find(function (b) {
                                                return String(b.id) === String(bid);
                                            });

                                            console.log('building created:', self.buildingCreated);
                                        }

                                        // Reset + close
                                        setTimeout(function () {
                                            self.closeCreateModal();
                                            self.clearCreateAlert();
                                        }, 800);

                                        self.resetFormKeepCoords();
                                        // if (bid) window.location.href = CONFIG.FAMILY_ADD_URL(bid);
                                    }


                                    if (action === 'edit-family') {
                                        var fid = btn.getAttribute('data-family-id');
                                        if (fid) window.location.href = CONFIG.FAMILY_EDIT_URL(fid);
                                    }

                                    if (action === 'delete-family') {
                                        var fid = btn.getAttribute('data-family-id');
                                        if (!fid) return;
                                        if (!confirm('Delete this family? This action cannot be undone.')) return;

                                        var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || null;
                                        var headers = { 'Accept': 'application/json' };
                                        if (csrf) headers['X-CSRF-TOKEN'] = csrf;

                                        // Laravel-friendly DELETE via POST + _method
                                        var body = new URLSearchParams({ _method: 'DELETE' });

                                        fetch(CONFIG.FAMILY_DELETE_URL(fid), {
                                            method: 'POST',
                                            headers: headers,
                                            body: body
                                        })
                                            .then(jsonSafe)
                                            .then(function (r) {
                                                if (r.error) {
                                                    alert(r.message || 'Failed to delete family.');
                                                    return;
                                                }
                                                // Remove locally and re-render
                                                families = families.filter(function (f) { return String(f.id) !== String(fid); });
                                                building.families = families;
                                                render();
                                            })
                                            .catch(function (err) {
                                                console.error('delete family error:', err);
                                                alert('Unexpected error while deleting family.');
                                            });
                                    }
                                };
                            })
                            .catch(function (err) {
                                console.error('building info fetch error:', err);
                                self.modalContent.innerHTML = '<p>Failed to load building info.</p>';
                            });

                    });

                    self.circles.push({ building: b, circle: circle });
                });
            },

            onMapClick: function (e) {
                // Location chosen
                var lat = e.latLng.lat().toFixed(6);
                var lng = e.latLng.lng().toFixed(6);

                // Always set coords
                this.form.latitude = lat;
                this.form.longitude = lng;

                if (this.createMode) {
                    // First map click after clicking "Create": open modal
                    this.openCreateModal();
                    this.createMode = false; // reset
                }
            },


            addFamily:function (building) {
                if (created.id) {
                    self.openFamilyCreateModal = 1;
                    self.buildingCreated = building;
                    console.error('create error:', self.buildingCreated);
                }
                // Reset + close
                setTimeout(function () {
                    self.closeCreateModal();
                    self.clearCreateAlert();
                }, 800);

                self.resetFormKeepCoords();
            },
            // ---------- SAVE ----------
            saveBuilding: function () {
                var self = this;

                // Sync form from inputs (since we didn’t v-model external DOM)
                var m = this.createContent;
                var getVal = function (id) { var el = qs(id, m); return el ? el.value : ''; };
                this.form.name = getVal('#f_name').trim();
                this.form.latitude = getVal('#f_latitude').trim();
                this.form.longitude = getVal('#f_longitude').trim();
                this.form.address = getVal('#f_address').trim();
                this.form.description = getVal('#f_description').trim();
                this.form.area_id = getVal('#f_area_id').trim();
                this.form.building_number = getVal('#f_building_number').trim();
                var fc = getVal('#f_floors_count').trim();
                this.form.floors_count = fc ? Number(fc) : null;

                // Basic checks
                if (!this.form.name) return this.showCreateAlert('Please enter a name.', 'danger');
                if (!this.form.latitude || !this.form.longitude) return this.showCreateAlert('Click on the map to choose a location.', 'danger');
                if (!this.form.area_id) return this.showCreateAlert('Please select an area.', 'danger');

                var csrf = getCSRFToken();
                var headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
                if (csrf) headers['X-CSRF-TOKEN'] = csrf;

                this.saving = true;
                fetch(CONFIG.BUILDINGS_CREATE_URL, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(this.form)
                })
                    .then(jsonSafe)
                    .then(function (data) {
                        if (data && data.error) {
                            self.showCreateAlert(data.message || 'Failed to save building.', 'danger');
                            return;
                        }
                        self.showCreateAlert((data && data.message) ? data.message : 'Building saved successfully.', 'success');

                        // Add to map immediately (optimistic UI)
                        var created = (data && data.result && data.result.data) ? data.result.data : {
                            id: Date.now(),
                            name: self.form.name,
                            latitude: self.form.latitude,
                            longitude: self.form.longitude
                        };
                        self.buildings.push(created);
                        self.drawBuildings();

                        if (created.id) {
                            self.openFamilyCreateModal = 1;
                            self.buildingCreated = created;
                            console.error('create error:', self.buildingCreated);
                        }
                        // Reset + close
                        setTimeout(function () {
                            self.closeCreateModal();
                            self.clearCreateAlert();
                        }, 800);

                        self.resetFormKeepCoords();
                    })
                    .catch(function (err) {
                        console.error('create error:', err);
                        self.showCreateAlert(err.message || 'Unexpected error.', 'danger');
                    })
                    .finally(function () {
                        self.saving = false;
                    });
            },

            resetFormKeepCoords: function () {
                var lat = this.form.latitude, lng = this.form.longitude;
                this.form = {
                    name: '',
                    latitude: lat,
                    longitude: lng,
                    address: '',
                    description: '',
                    area_id: '',
                    building_number: '',
                    floors_count: null,
                };
            },

            showCreateAlert: function (msg, kind) {
                var box = qs('#createBuildingAlert', this.createContent);
                if (!box) return;
                box.style.display = 'block';
                box.style.padding = '10px 12px';
                box.style.borderRadius = '6px';
                box.style.marginBottom = '10px';
                box.style.color = (kind === 'success') ? '#155724' : '#721c24';
                box.style.background = (kind === 'success') ? '#d4edda' : '#f8d7da';
                box.style.border = '1px solid ' + ((kind === 'success') ? '#c3e6cb' : '#f5c6cb');
                box.innerText = msg;
            },

            clearCreateAlert: function () {
                var box = qs('#createBuildingAlert', this.createContent);
                if (box) { box.style.display = 'none'; box.innerText = ''; }
            },
        }
    });
})();
