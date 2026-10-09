@extends('layouts.backend')

@section('title', 'Users')

@section('content')
    <div class="page">
        <div class="back-top">
            <div>
                <p class="back-title">Users</p>
                <p class="back-description">Manage staff accounts and roles. Accounts are deactivated, never deleted.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="back-add-button">
                <i class="bi bi-plus-lg"></i>
                <span>Add New User</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form" class="filter-form">
            <input type="text" name="search" value="{{ $filter['search'] ?? '' }}" placeholder="Name or email" class="input filter-input">

            <select name="role" class="input filter-input">
                <option value="">- All Roles -</option>
                @foreach (App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(($filter['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>

            <select name="status" class="input filter-input">
                <option value="">- All Status -</option>
                @foreach (App\Enums\Status::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filter['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>

            <a href="{{ route('admin.users.index') }}" class="clear-filter-button">
                <i class="bi bi-x-circle"></i>
                <span>Clear</span>
            </a>
        </form>

        <div class="relative data-table-container custom-scrollbar">
            <x-table-loader />

            <table class="data-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @include('admin.users._table')
                </tbody>
            </table>
        </div>

        <div class="mt-3 sm:mt-5">
            {{ $items->links() }}
        </div>
    </div>
@endsection

@push('after-scripts')
    @vite(['resources/js/index.js'])
@endpush
