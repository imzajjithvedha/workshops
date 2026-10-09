@extends('layouts.backend')

@section('title', 'Add Workshop')

@section('content')
    <form action="{{ route('workshops.store') }}" method="POST">
        @csrf
        @include('workshops._form')
    </form>
@endsection
