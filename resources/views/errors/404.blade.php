@extends('layouts.frontend')

@section('title', 'Page Not Found')

@section('content')
    <section class="wrapper page-padding flex items-center justify-center">
        <div class="text-center">
            <p class="text-6xl md:text-8xl font-bold text-(--color-dark-red)">
                404
            </p>

            <h1 class="mt-3 md:mt-5 text-2xl md:text-4xl font-bold text-(--color-primary)">
                Page Not Found
            </h1>

            <p class="mt-3 md:mt-5 text-gray-600 leading-7 text-[15px] md:text-base">
                Sorry, we couldn't find the page you're looking for.
                It may have been moved, removed, or the link may be incorrect.
            </p>

            <div class="mt-5 md:mt-10">
                <a href="{{ route('home') }}" class="inline-block submit-button">
                    <i class="bi bi-house"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </section>
@endsection
