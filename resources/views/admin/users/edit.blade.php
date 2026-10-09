@extends('layouts.backend')

@section('title', 'Edit User')

@section('content')
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')
        @include('admin.users._form')
    </form>
@endsection
