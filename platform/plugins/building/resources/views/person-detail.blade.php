@extends('core/base::layouts.master')

@section('content')
<div class="container">
    <h2>{{ $person->name }}</h2>
    @if($person->image)
        <img src="{{ RvMedia::getImageUrl($person->image) }}" alt="{{ $person->name }}" style="max-width:200px;">
    @endif

    <p>{{ $person->full_info }}</p>

    @if($person->gallery)
        <h4>Gallery</h4>
        @foreach($person->gallery as $img)
            <img src="{{ RvMedia::getImageUrl($img) }}" style="max-width:150px;">
        @endforeach
    @endif

    @if($person->audio_links)
        <h4>Audio</h4>
        @foreach($person->audio_links as $audio)
            <audio controls>
                <source src="{{ $audio }}" type="audio/mpeg">
            </audio>
        @endforeach
    @endif

    @if($person->video_links)
        <h4>Videos</h4>
        @foreach($person->video_links as $video)
            <a href="{{ $video }}" target="_blank">{{ $video }}</a><br>
        @endforeach
    @endif
</div>
@endsection
