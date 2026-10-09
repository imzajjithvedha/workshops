@extends('layouts.backend')

@section('title', 'Activity Log')

@section('content')
    <div class="page">
        <div class="back-top">
            <div>
                <p class="back-title">Activity Log</p>
                <p class="back-description">Every change made on the platform: who did what, and when.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="filter-form" class="filter-form">
            <select name="area" class="input filter-input">
                <option value="">- Everything -</option>
                <option value="accounts" @selected(($filter['area'] ?? '') === 'accounts')>Accounts & Roles</option>
                <option value="workshops" @selected(($filter['area'] ?? '') === 'workshops')>Workshops</option>
                <option value="registrations" @selected(($filter['area'] ?? '') === 'registrations')>Registrations</option>
            </select>

            <a href="{{ route('admin.activity-logs.index') }}" class="clear-filter-button">
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
                        <th>WHEN</th>
                        <th>WHO</th>
                        <th>ACTION</th>
                        <th>RECORD</th>
                    </tr>
                </thead>
                <tbody>
                    @include('admin.activity-logs._table')
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
