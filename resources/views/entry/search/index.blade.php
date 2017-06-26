@extends('layouts.app')

@section('title', 'PHP Document Search')

@section('content')
    <h1>PHP Document Search</h1>

    <form action="{{ route('search') }}" method="get">
        <input name="query" type="search" value="{{ $query }}"></input>
    </form>

    <div class="entries">
        @foreach ($entries as $entry)
            <a href="{{ $entry->url }}">
                <h4>
                     {!! $entry->highlighted_title !!}
                     <span class="score">{{ $entry->score }}</span>
                </h4>
                @foreach ($entry->arrayValue('content_snippets') as $snippet)
                    <pre class="snippet">{!! $snippet !!}</pre>
                @endforeach
           </a>
        @endforeach
    </div>
@endsection
