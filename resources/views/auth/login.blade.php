@extends('layouts.frontend')

@section('title', 'Login to Your Account')

@section('content')
    <div class="wrapper page-padding auth-page">
        <div class="width">
            <h1 class="title">Welcome Back</h1>
            <p class="description">Log in to manage workshops and register attendees.</p>

            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <div class="grid mb-4 md:mb-5">
                    <label for="email" class="label">Email Address<span class="asterisk">*</span></label>

                    <input type="email" class="input" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required autofocus autocomplete="username">

                    <x-input-error field="email"></x-input-error>
                </div>

                <div class="grid mb-2.5 md:mb-3 relative">
                    <label for="password" class="label">Password<span class="asterisk">*</span></label>
                    <input type="password" class="input" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                    <span class="bi bi-eye-slash-fill toggle-password"></span>
                    <x-input-error field="password"></x-input-error>
                </div>

                <div class="flex justify-between items-center mb-4 md:mb-5">
                    <div class="flex items-center gap-1.5">
                        <input type="checkbox" class="w-3 h-3 md:w-4.25 md:h-4.25 accent-(--color-primary)" id="remember" name="remember">
                        <label class="text-sm cursor-pointer hover:font-medium" for="remember">Remember Me</label>
                    </div>

                    <span class="text-sm text-gray-500">Forgot your password? Ask your administrator.</span>
                </div>

                <button type="submit" class="submit-button">Login Now</button>
            </form>

            <hr class="w-full min-[400px]:w-[40%] md:w-[25%] border-(--color-border) mx-auto my-3">

            <p class="text">
                Accounts are created by your administrator. There is no public sign-up.
            </p>
        </div>
    </div>
@endsection
