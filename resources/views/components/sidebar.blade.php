@php
    $user = auth()->user();
    $segment = Request::segment(1) === 'admin' ? Request::segment(2) : Request::segment(1);
    $sub = Request::segment(2);
@endphp

<button id="sidebarToggle" type="button" class="text-white rounded min-[1100px]:hidden fixed top-4.5 left-6 z-10000 cursor-pointer" aria-label="Toggle sidebar">
    <i class="bi bi-layout-sidebar-inset text-xl"></i>
</button>

<div id="sidebar" class="z-10000 fixed top-14 hidden h-[calc(100vh-60px)] w-62.5 flex-col overflow-y-auto bg-(--color-primary) min-[1100px]:top-20 min-[1100px]:flex min-[1100px]:h-[calc(100vh-80px)]">
    <nav class="flex flex-col overflow-y-auto p-5 gap-4">
        @if ($user->canManageUsers())
            <div>
                <p class="sidebar-label">Platform</p>
                <x-sidebar-item :href="route('admin.users.index')" :active="$segment == 'users'" icon="bi-people" label="Users" />
            </div>

            <div>
                <p class="sidebar-label">System</p>
                <x-sidebar-item :href="route('admin.activity-logs.index')" :active="$segment == 'activity-logs'" icon="bi-clock-history" label="Activity Log" />
            </div>
        @endif

        @if ($user->canHandleRegistrations())
            <div>
                <p class="sidebar-label">Workshops</p>
                <x-sidebar-item :href="route('workshops.index')" :active="$segment == 'workshops' && $sub !== 'create'" icon="bi-calendar-week" label="All Workshops" />
                <x-sidebar-item :href="route('workshops.index', ['from' => today()->toDateString(), 'to' => today()->endOfWeek()->toDateString(), 'has_seats' => 1])" icon="bi-ticket-perforated" label="Seats This Week" />

                @if ($user->canManageWorkshops())
                    <x-sidebar-item :href="route('workshops.create')" :active="$sub === 'create'" icon="bi-plus-circle" label="Add Workshop" />
                @endif
            </div>
        @endif
    </nav>

    <div class="mt-auto border-t border-white/10 p-5">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-item sidebar-logout w-full">
                <i class="bi bi-power"></i>
                Log Out
            </button>
        </form>
    </div>
</div>
