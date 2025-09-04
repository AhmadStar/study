<!-- map.blade.php -->
@extends('core/base::layouts.master')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">


    <div id="app" dir="rtl" style="max-width:1200px;margin:12px auto">
        <!-- Building type filters (all UNCHECKED by default) -->
        <div class="filters-bar" id="typeFilters">
            <strong style="margin-inline-end:6px">نوع البناء:</strong>

            <div class="pill-filter" v-for="t in buildingTypes" :key="t.value">

                    {{--<input type="checkbox"  :value="t.value" v-model="activeTypes">--}}
                    {{--<label :style="pillStyle(t.value, activeTypes.includes(t.value))">--}}
                        {{--<i :class="t.icon"></i>--}}
                        {{--<span>@{{ t.label }}</span>--}}
                    {{--</label>--}}

                <label class="pill"
                       :style="pillStyle(t.value, activeTypes.includes(t.value))">
                    <input type="checkbox" :value="t.value" v-model="activeTypes">
                    <span class="dot" :style="{background: typeColors[t.value] || typeColors.other}"></span>
                    <i :class="t.icon"></i>
                    <span>@{{ t.label }}</span>
                </label>
            </div>

            <div class="filters-actions">
            <button type="button" class="pill" @click="activeTypes=[]" style="margin-inline-start:auto">مسح الأنواع</button>
            <button type="button" class="pill" @click="selectAllTypes">تحديد الكل</button>
                </div>
        </div>

        <!-- Extra filters -->
        <div class="filters-bar" id="extraFilters" style="margin-top:8px">
            <div class="pill">
                <label for="filter_is_empty" style="margin:0">حالة الإشغال</label>
                <select id="filter_is_empty" class="form-select" style="min-width:130px" v-model="isEmpty">
                    <option value="">الكل</option>
                    <option value="1">فارغ</option>
                    <option value="0">غير فارغ</option>
                </select>
            </div>

            <div class="pill">
                <label for="filter_floors_count" style="margin:0">عدد الطوابق</label>
                <input id="filter_floors_count" type="number" min="0" class="form-control"
                       placeholder="مثال: 3" style="width:120px" v-model.number="floorsCount">
            </div>
            <div class="pill" style="flex:1; min-width:220px">
                <label for="filter_family_head" style="margin:0">اسم ربّ الأسرة</label>
                <input id="filter_family_head"
                       type="text"
                       class="form-control"
                       placeholder="ابحث باسم ربّ الأسرة…"
                       v-model.trim="familyHead">
            </div>
            <!-- Optional: only-with-families toggle (default ON) -->
            {{--<div class="pill">--}}
                {{--<label class="d-inline-flex align-items-center" style="gap:6px;margin:0">--}}
                    {{--<input type="checkbox" v-model="onlyWithFamilies">--}}
                    {{--عرض المباني التي تحتوي عائلات فقط--}}
                {{--</label>--}}
            {{--</div>--}}
            <div class="filters-actions">
            <button type="button" class="pill" @click="resetFilters">إعادة الضبط</button>
            <span v-if="loading" style="margin-inline-start:auto">جارِ التحميل…</span>
                </div>
        </div>

        <div class="sep"></div>

        <!-- Map -->
        <div id="map" style="height:600px;border-radius:8px;overflow:hidden"></div>

        {{-- Residents Modal HTML (if you use it later) --}}
        <div id="residentsModalOverlay">
            <div id="residentsModal">
                <span id="residentsModalClose">&times;</span>
                <div id="residentsModalContent"></div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
{{-- Google Maps JS API --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBokt_jID9DLiGm7hbjYfVojPRUnXE-2ig"></script>
{{-- Vue 2 (use ONE copy; removed duplicate CDN include) --}}
<script src="https://study.alkhaleej-best.com/public/themes/ripple/js/vue.js"></script>

<script>
    // debounce helper
    function debounce(fn, wait=300){let t;return function(...a){clearTimeout(t);t=setTimeout(()=>fn.apply(this,a),wait)}}

    new Vue({
        el: '#app',
        data: {
            apiUrl: '/admin/all-buildings',

            // colors
            typeColors:{
                security_point:'#00bcd4',
                residential:'#28a745',
                commercial:'#0d6efd',
                usable_hq:'#ffc107',
                slaughter_site:'#dc3545',
                security_event:'#6c757d',
                military_point:'#6610f2',
                other:'#7a7a7a',
            },
            statusColors:{
                danger:'#FF0000',
                high_risk:'#FF4500',
                moderate:'#FFD700',
                safe:'#00FF00',
                evacuated:'#0000FF',
            },

            // building types + icons
            buildingTypes: [
                { value: 'residential',    label: 'سكني',            icon:'ti ti-home' },
                { value: 'commercial',     label: 'تجاري',           icon:'ti ti-building-store' },
                { value: 'security_point', label: 'نقطة أمنية',      icon:'ti ti-shield' },
                { value: 'usable_hq',      label: 'مقر قابل للاستخدام', icon:'ti ti-building-community' },
                { value: 'slaughter_site', label: 'موقع مجزرة',      icon:'ti ti-alert-triangle' },
                { value: 'security_event', label: 'حدث أمني',        icon:'ti ti-bell' },
                { value: 'military_point', label: 'نقطة عسكرية',     icon:'ti ti-target' },
            ],

            activeTypes: [],  // all unchecked by default
            isEmpty: '',
            floorsCount: '',
            familyHead: '',
//            onlyWithFamilies: true,
            map: null,
            defaultCenter: { lat: 33.481289, lng: 36.311463 },
            defaultZoom: 16,
            markers: [],
            infoWindow: null,

            loading: false,
            abortCtl: null,

            routes: {
                infoBase: '/building-info'          // GET /building-info/{id}
            },
            modalOverlay: null,
            modalContent: null,
            modalClose: null,

            // animation config
            pulseMinScale: 6,
            pulseMaxScale: 9,
            pulseStep: 0.15,
            pulseIntervalMs: 50,

        },

        mounted(){
            // init map
            this.map = new google.maps.Map(document.getElementById('map'), {
                center: this.defaultCenter, zoom: this.defaultZoom, mapTypeId: 'satellite'
            });
            this.infoWindow = new google.maps.InfoWindow();

            // initial load (no filters → all)
            this.fetchAndRender();

            // watch filters
            this.$watch('activeTypes', this.debouncedFetch, { deep:true });
            this.$watch('isEmpty', this.debouncedFetch);
            this.$watch('floorsCount', this.debouncedFetch);


            this.modalOverlay = document.getElementById('residentsModalOverlay');
            this.modalContent = document.getElementById('residentsModalContent');
            this.modalClose   = document.getElementById('residentsModalClose');

            this.$watch('familyHead', this.debouncedFetch);
            this.$watch('onlyWithFamilies', this.debouncedFetch);

            if (this.modalClose) this.modalClose.addEventListener('click', ()=>this.closeModal());
            if (this.modalOverlay) this.modalOverlay.addEventListener('click', e=>{
                if (e.target === this.modalOverlay) this.closeModal();
        });
        },

        methods:{
            resetFilters(){
                this.activeTypes=[]; this.isEmpty=''; this.floorsCount='';
                this.familyHead=''; this.onlyWithFamilies=true;
                this.fetchAndRender();
            },
            buildUrl(){
                const url = new URL(this.apiUrl, window.location.origin);
                if (this.activeTypes.length) this.activeTypes.forEach(t=>url.searchParams.append('building_type[]', t));
                if (this.isEmpty !== '') url.searchParams.set('is_empty', this.isEmpty);
                if (this.floorsCount !== '' && this.floorsCount != null) url.searchParams.set('floors_count', this.floorsCount);
                if (this.familyHead !== '' && this.familyHead != null) url.searchParams.set('family_head', this.familyHead);
//                if (this.onlyWithFamilies) url.searchParams.set('only_with_families', '1');
                return url.toString();
            },
            // UI helpers
            selectAllTypes(){ this.activeTypes = this.buildingTypes.map(t=>t.value); this.fetchAndRender(); },

            debouncedFetch: debounce(function(){ this.fetchAndRender(); }, 250),

            pillStyle(type, isActive){
                const c = this.typeColors[type] || this.typeColors.other;
                return isActive
                        ? { borderColor:c, background:this.alpha(c,.12), boxShadow:`0 0 0 2px ${this.alpha(c,.15)} inset` }
                        : { borderColor:'#d0d7de', background:'#fff' };
            },
            alpha(hex,a){
                const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex); if(!m) return hex;
                const r=parseInt(m[1],16), g=parseInt(m[2],16), b=parseInt(m[3],16);
                return `rgba(${r},${g},${b},${a})`;
            },

            // data fetch
//            buildUrl(){
//                const url = new URL(this.apiUrl, window.location.origin);
//                if (this.activeTypes.length) this.activeTypes.forEach(t=>url.searchParams.append('building_type[]', t));
//                if (this.isEmpty !== '') url.searchParams.set('is_empty', this.isEmpty);
//                if (this.floorsCount !== '' && this.floorsCount != null) url.searchParams.set('floors_count', this.floorsCount);
//                return url.toString();
//            },

            fetchAndRender(){
                const url = this.buildUrl();
                if (this.abortCtl) this.abortCtl.abort();
                this.abortCtl = new AbortController();

                this.loading = true;
                fetch(url, { headers:{'Accept':'application/json'}, signal:this.abortCtl.signal })
                        .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
            .then(json => {
                    const buildings = Array.isArray(json) ? json : (json.data || []);
                this.drawMarkers(buildings);
            })
            .catch(err => { if (err.name !== 'AbortError') console.error(err); })
            .finally(() => { this.loading = false; this.abortCtl = null; });
            },

            // markers
            drawMarkers(buildings){
                // clear old markers + timers
                this.clearMarkers();

                const bounds = new google.maps.LatLngBounds();

                buildings.forEach(b => {
                    const lat = Number(b.latitude), lng = Number(b.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                const t = b.building_type || 'other';
                const fill = this.typeColors[t] || this.typeColors.other;
                const ring = (b.status && this.statusColors[b.status]) ? this.statusColors[b.status] : '#ffffff';

                // base icon (colored by building_type)
                const baseIcon = {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: this.pulseMinScale,
                    fillColor: fill,
                    fillOpacity: 0.95,
                    strokeWeight: 2,
                    strokeColor: ring
                };

                const marker = new google.maps.Marker({
                    position: { lat, lng },
                    map: this.map,
                    title: b.name || ('#'+b.id),
                    icon: baseIcon
                });

                // animated pulse
                let s = this.pulseMinScale;
                let dir = +1;
                marker.__pulseTimer = setInterval(()=>{
                    s += dir * this.pulseStep;
                if (s >= this.pulseMaxScale) dir = -1;
                if (s <= this.pulseMinScale) dir = +1;
                marker.setIcon(Object.assign({}, baseIcon, { scale: s }));
            }, this.pulseIntervalMs);

                marker.addListener('click', () => this.openBuildingModal(b.id));

                this.markers.push(marker);
                bounds.extend(marker.getPosition());
            });

                if (buildings.length){
                    this.map.fitBounds(bounds);
                    if (this.map.getZoom() > 19) this.map.setZoom(19);
                } else {
                    this.map.setCenter(this.defaultCenter);
                    this.map.setZoom(this.defaultZoom);
                }
            },


            clearMarkers(){
                this.markers.forEach(m => {
                    if (m.__pulseTimer) clearInterval(m.__pulseTimer);
                m.setMap(null);
            });
                this.markers = [];
            },
            escape(s){ return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); },

            openBuildingModal(id){

                if (!this.modalOverlay || !this.modalContent) return;
                this.modalContent.innerHTML = '<p>جاري تحميل البيانات…</p>';
                this.modalOverlay.style.display = 'flex';

                fetch(`${this.routes.infoBase}/${id}`, { headers:{'Accept':'application/json'} })
                        .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
            .then(resp=>{
                    const building = resp.data ? resp.data : resp;
                const families = Array.isArray(building.families) ? building.families : [];
                this.renderBuildingModal(building, families);
            })
            .catch(err=>{
                    console.error(err);
                this.modalContent.innerHTML = '<p>تعذر تحميل معلومات البناء.</p>';
            });
            },

            closeModal(){
                if (this.modalOverlay) this.modalOverlay.style.display = 'none';
                if (this.modalContent) this.modalContent.innerHTML = '';
            },

            renderBuildingModal(building, families){
                const n = (v)=> (v==null || v==='') ? '—' : this.escape(String(v));
                const yn = (x)=> x ? 'نعم' : 'لا';
                const typ = building.building_type ? (this.buildingTypes.find(t=>t.value===building.building_type)?.label || building.building_type) : '—';

                const header = `
    <div class="build-header">
      <div>
        <h3 style="margin:0">${n(building.name)}</h3>
        <div style="margin-top:6px;font-size:13px;color:#555">
          <span class="badge"><strong>النوع:</strong> ${n(typ)}</span>
          <span class="badge"><strong>رقم المبنى:</strong> ${n(building.building_number)}</span>
          <span class="badge"><strong>عدد الطوابق:</strong> ${building.floors_count ?? '—'}</span>
          <span class="badge"><strong>فارغ؟</strong> ${yn(Number(building.is_empty))}</span>
        </div>
        ${building.address ? `<div style="margin-top:6px"><strong>العنوان:</strong> ${n(building.address)}</div>` : ''}
      </div>
    </div>
    <hr>
  `;

                const famCards = !families.length ? `
      <div class="card" style="background:#fff3cd;border-color:#ffeeba;color:#856404">
        لا توجد عائلات في هذا المبنى.
      </div>
    ` : families.map(f=>{
                    // family_members in your sample is array of arrays [{key,value},...]
                    const membersList = Array.isArray(f.family_members)
                            ? f.family_members.map(row=>{
                        const obj = Object.fromEntries(row.map(p=>[p.key,p.value]));
                return `
              <li style="margin-bottom:2px">
                <span>${n(obj.full_name || '')}</span>
                <small style="color:#666"> — ${n(obj.gender || '')} • ${n(obj.marital_status || '')} • ${n(obj.birth_date || '')}</small>
              </li>
            `;
            }).join('')
            : '';

                return `
        <div class="card">
          <div class="card-head">
            <div style="min-width:0">
              <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                ${n(f.head_name || f.name || 'عائلة')}
                ${f.house_type ? `<span class="badge">${n(f.house_type)}</span>` : ''}
              </div>
              <div class="card-meta">
                <span><strong>عدد الأفراد:</strong> ${f.count_family_members ?? '—'}</span>
                ${f.family_code ? `<span style="margin-inline-start:10px"><strong>الرمز:</strong> ${n(f.family_code)}</span>` : ''}
                ${f.floor_number != null ? `<span style="margin-inline-start:10px"><strong>الطابق:</strong> ${n(f.floor_number)}</span>` : ''}
              </div>
              ${f.address ? `<div class="card-meta">${n(f.address)}</div>` : ''}
              ${f.phone || f.mobile ? `<div class="card-meta"><strong>اتصال:</strong> ${n(f.phone || f.mobile)}</div>` : ''}
            </div>
          </div>
          ${
                        membersList
                                ? `<div style="margin-top:8px"><strong>أفراد العائلة:</strong><ul style="margin:6px 0 0 0;padding:0 18px">${membersList}</ul></div>`
                                : ''
                        }
          ${f.notes ? `<div style="margin-top:8px;background:#f8f9fa;border:1px dashed #dee2e6;border-radius:6px;padding:8px">${n(f.notes)}</div>` : ''}
        </div>
      `;
            }).join('');

                this.modalContent.innerHTML = header + famCards;
            },
        }
    });
</script>

<style>
    .build-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
    .badge{display:inline-block;background:#f1f3f5;border:1px solid #e5e7eb;border-radius:999px;padding:4px 8px;margin-inline-start:6px;font-size:12px}
    .card{border:1px solid #e9ecef;border-radius:10px;padding:10px 12px;margin-bottom:10px;background:#fff}
    .card-head{display:flex;justify-content:space-between;align-items:center;gap:8px}
    .card-meta{font-size:13px;color:#555;margin-top:3px}
    @media (max-width:768px){.build-header,.card-head{flex-wrap:wrap}}
    /* Pills & filters */
    div#typeFilters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .filters-actions button:hover {
        background: #f8f9fa;
    }
    [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
        cursor: pointer;
    }

    .filters-actions button {
        border: 1px solid #ced4da;
        background: #fff;
        border-radius: 8px;
        padding: 6px 10px;
        cursor: pointer;
        font-size: 13px;
    }

    .filters-bar {
        display: flex; flex-wrap: wrap; gap: 8px;
        margin: 10px 0 14px; align-items: center;
    }
    .pill-filter { position: relative; }
    .pill-filter input[type="checkbox"] {
        position: absolute; opacity: 0; pointer-events: none;
        width: 100%;
        height: 50px;
    }
    .pill-filter label {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 12px; border-radius: 999px;
        border: 1px solid #d0d7de; cursor: pointer; user-select: none;
        background: #fff; font-weight: 600; font-size: 13px;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
        transition: all .18s ease-in-out;
    }
    .pill-filter label i { font-size: 16px; }
    .pill-filter input:checked + label {
        background: linear-gradient(180deg,#f0f7ff,#e2f0ff);
        border-color: #9ed0ff; box-shadow: 0 2px 6px rgba(30,144,255,.18);
    }
    .pill-filter input:focus + label { outline: 2px solid #9ed0ff55; }
    .pill-filter label .count { font-weight: 700; opacity: .6; }
    .filters-actions {
        margin-inline-start: auto; display: flex; gap: 8px;
    }
    .filters-actions button {
        border: 1px solid #ced4da; background: #fff; border-radius: 8px;
        padding: 6px 10px; cursor: pointer; font-size: 13px;
    }
    .filters-actions button:hover { background: #f8f9fa; }
    .pill-filter input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* Residents Modal (kept as-is) */
    #residentsModalContent p{margin-bottom:5px}
    .hr,hr{margin:1rem 0}
    #residentsModalContent h3{color:rgb(13,110,253)}
    #residentsModalOverlay{position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.5);display:none;justify-content:center;align-items:center;z-index:10000}
    #residentsModal{background:#fff;width:90%;max-width:600px;max-height:80vh;overflow-y:auto;border-radius:8px;padding:20px;font-family:Arial,sans-serif;box-shadow:0 2px 10px rgba(0,0,0,.3);position:relative}
    #residentsModalClose{position:absolute;top:5px;left:15px;font-size:28px;color:#888;cursor:pointer;user-select:none}
    #residentsModalClose:hover{color:#333}
    #residentsModal ul{list-style:none;padding:0;margin:0}
    #residentsModal li{display:flex;align-items:center;margin-bottom:10px}
    #residentsModal img.avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:12px}
    #residentsModal .avatar-placeholder{width:40px;height:40px;border-radius:50%;background:#ccc;color:#555;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:bold;margin-right:12px}
    #residentsModal a{color:#007BFF;text-decoration:none;font-weight:600}
    #residentsModal a:hover{text-decoration:underline}
</style>

@endpush
