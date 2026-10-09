@php
    $fixed ??= false;
    $user = auth()->user();
    $segment = Request::segment(1) === 'admin' ? Request::segment(2) : Request::segment(1);
@endphp

<header>
    <div class="bg-(--color-primary) {{ $fixed ? 'fixed w-full z-9999' : '' }}">
        <div class="hidden min-[1200px]:flex wrapper-fluid justify-between items-center">
            <a href="{{ route('home') }}" class="py-4">
                <x-logo />
            </a>

            <ul class="flex items-center gap-8">
                @auth
                    <li class="cursor-pointer relative profile-dropdown">
                        <div class="flex items-center gap-2.5">
                            <span class="navbar-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>

                            <div>
                                <p class="text-white text-[15px] font-medium leading-tight">{{ $user->name }}</p>
                                <p class="text-white/60 text-xs">{{ $user->role->label() }}</p>
                            </div>
                            <i class="bi bi-chevron-down profile-chevron"></i>
                        </div>

                        <ul class="profile-dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route($user->role->dashboardRoute()) }}">
                                    <i class="bi bi-grid-1x2"></i>Dashboard
                                </a>
                            </li>

                            <li>
                                <hr class="border-white/10 mx-4 my-1">
                            </li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-power"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>

        <div class="min-[1200px]:hidden flex wrapper-fluid justify-between items-center py-1 border-b border-white/10">
            <a href="{{ route('home') }}" class="py-1.5 {{ $fixed ? 'ms-15' : '' }}">
                <x-logo />
            </a>

            <button id="mobile-menu-toggle" type="button" class="text-white text-2xl focus:outline-none cursor-pointer" aria-label="Toggle navigation">
                <i id="menu-icon" class="bi bi-list"></i>
            </button>
        </div>

        <div id="mobile-menu" class="hidden min-[1200px]:hidden text-white px-7 py-5">
            @auth
                <div class="flex items-center gap-3 mb-5">
                    <span class="navbar-avatar w-10! h-10! text-base!">{{ strtoupper(substr($user->name, 0, 1)) }}</span>

                    <div>
                        <p class="text-white text-[15px] font-medium">{{ $user->name }}</p>
                        <span class="text-xs text-white/60">{{ $user->role->label() }}</span>
                    </div>
                </div>

                <ul class="flex flex-col">
                    <li>
                        <a class="dropdown-item" href="{{ route($user->role->dashboardRoute()) }}">
                            <i class="bi bi-grid-1x2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-power"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</header>
