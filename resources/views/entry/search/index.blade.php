@extends('layouts.app')

@section('title', 'PHP document search')

@section('content')
    <h1>PHP document search</h1>

    <form action="{{ route('search') }}" method="get">
        <input id="query" name="query" type="search" value="{{ $query }}">
        <input type="submit">
    </form>

    <div class="entries">
        @foreach ($entries as $entry)
            <a href="{{ $entry->url }}">
                <h4>
                     {!! $entry->highlighted_title !!}
                     <span class="score">{{ $entry->score }}</span>
                </h4>
                @foreach ($entry->content_snippets as $snippet)
                    <pre class="snippet">{!! $snippet !!}</pre>
                @endforeach
           </a>
        @endforeach
    </div>
@endsection
