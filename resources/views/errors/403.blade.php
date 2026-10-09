@extends('layouts.frontend')

@section('title', 'Not Allowed')

@section('content')
    <section class="wrapper page-padding flex items-center justify-center">
        <div class="text-center">
            <p class="text-6xl md:text-8xl font-bold text-(--color-dark-red)">
                403
            </p>

            <h1 class="mt-3 md:mt-5 text-2xl md:text-4xl font-bold text-(--color-primary)">
                You Can't Do That
            </h1>

            <p class="mt-3 md:mt-5 text-gray-600 leading-7 text-[15px] md:text-base">
                {{ $exception->getMessage() ?: 'Your role does not allow this page or action.' }}
            </p>

            <div class="mt-5 md:mt-10">
                @auth
                    <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="inline-block submit-button">
                        <i class="bi bi-house"></i>
                        Back to My Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-block submit-button">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection
