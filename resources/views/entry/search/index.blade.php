@extends('layouts.app')

@section('title', 'PHP Document Search')

@section('content')
    <h1>PHP Document Search</h1>

    <ol>
        @foreach ($entries as $entry)
            <li>{{ $entry->title }}: {{ $entry->content }}</li>
        @endforeach
    </ol>
@endsection
