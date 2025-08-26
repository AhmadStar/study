@extends('core/base::layouts.master')

@section('content')
    {{-- CSRF for AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Residents Modal overlay */
        #residentsModalContent p{
            margin-bottom: 5px;
        }
        .hr, hr {
            margin: 1rem 0;
        }
        #residentsModalContent h3{
            color: rgb(13, 110, 253);
        }
        #residentsModalOverlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: none; /* hidden initially */
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        /* Residents Modal container */
        #residentsModal {
            background: white;
            width: 90%; max-width: 600px;
            max-height: 80vh; overflow-y: auto;
            border-radius: 8px; padding: 20px;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 10px rgba(0,0,0,.3);
            position: relative;
        }
        /* Close button */
        #residentsModalClose {
            position: absolute;
            top: 5px; left: 15px;
            font-size: 28px; color: #888;
            cursor: pointer; user-select: none;
        }
        #residentsModalClose:hover { color: #333; }
        /* List styling inside modal (avatars, links) */
        #residentsModal ul { list-style: none; padding: 0; margin: 0; }
        #residentsModal li { display: flex; align-items: center; margin-bottom: 10px; }
        #residentsModal img.avatar {
            width: 40px; height: 40px; border-radius: 50%;
            object-fit: cover; margin-right: 12px;
        }
        #residentsModal .avatar-placeholder {
            width: 40px; height: 40px; border-radius: 50%;
            background: #ccc; color: #555;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: bold; margin-right: 12px;
        }
        #residentsModal a { color: #007BFF; text-decoration: none; font-weight: 600; }
        #residentsModal a:hover { text-decoration: underline; }
    </style>

    <div id="mapBuilding">
        {{-- Map container (button injected by JS) --}}
        <div id="map" style="width: 100%; height: 600px; border-radius: 8px; overflow: hidden;"></div>

        {{-- Residents Modal HTML (used by map-vue.js) --}}
        <div id="residentsModalOverlay">
            <div id="residentsModal">
                <span id="residentsModalClose">&times;</span>
                <div id="residentsModalContent"></div>
            </div>
        </div>
        @include('plugins/building::partials.family-create-modal')

    </div>

@endsection

@push('footer')
<script>
    <?php  $areas = \Botble\Area\Models\Area::query()
            ->select(['id', 'name'])
            ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
            ->orderBy('name')
            ->get(); ?>
            window.areas= {!! collect($areas)->map(function($b){
                return [
                    'id' => $b->id,
                    'name' => $b->name,

                ];
            })->values()->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
            window.ROUTES = {
                areas: "{{ route('building.areas.index') }}",
                create: "{{ route('building.create.from.map') }}",
                residentsBase: "{{ url('/building-info') }}", // used as /building/{id}/residents

                // Admin bases (adjust if your admin prefix is different)
                buildingEditBase: "{{ url('/admin/buildings') }}", // /{id}/edit
                familyEditBase: "{{ url('/admin/families') }}",   // /{id}/edit
                familyDeleteBase: "{{ url('/admin/families') }}", // /{id} with DELETE
            };
</script>

{{-- Optionally pass buildings to JS to avoid an extra request --}}
@if(!empty($buildings))
    <script>
        window.BUILDINGS = {!! collect($buildings)->map(function($b){
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'latitude' => (float) $b->latitude,
                    'longitude' => (float) $b->longitude,
                ];
            })->values()->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
    </script>
@endif

{{-- Google Maps JS API --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBokt_jID9DLiGm7hbjYfVojPRUnXE-2ig"></script>

{{-- Vue 2 (same build your theme uses) --}}
<script src="https://study.alkhaleej-best.com/public/themes/ripple/js/vue.js"></script>

{{-- Main map logic (create button, modal, save building, residents, circles) --}}
<script src="https://study.alkhaleej-best.com/public/themes/ripple/js/map-vue.js?v={{ time() }}"></script>
@endpush
