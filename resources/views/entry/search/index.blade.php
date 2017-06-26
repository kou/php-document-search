@extends('layouts.app')

@section('title', 'PHP Document Search')

@section('content')
    <h1>PHP Document Search</h1>

    <form action="{{ route('search') }}" method="get">
        <input name="query" type="search" value="{{ $query }}"></input>
    </form>

    <ol>
        @foreach ($entries as $entry)
            <li>{{ $entry->title }}({{ $entry->score }}): {{ $entry->content }}</li>
        @endforeach
    </ol>
@endsection
