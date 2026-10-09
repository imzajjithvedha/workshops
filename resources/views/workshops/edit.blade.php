@extends('layouts.backend')

@section('title', "Edit {$workshop->code}")

@section('content')
    <form action="{{ route('workshops.update', $workshop) }}" method="POST">
        @csrf
        @method('PATCH')
        @include('workshops._form')
    </form>
@endsection
