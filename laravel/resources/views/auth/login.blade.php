@extends('layouts.app')

@section('title', 'Log in')

@push('styles')
    @vite('resources/css/pages/login.css')
@endpush

@section('content')
<div class="loginPage">
    <div class="loginCard">
        <h1 class="loginTitle">Log In</h1>

        <form method="POST" action="{{ route('login') }}" class="loginForm">
            @csrf

            <div class="loginFormGroup">
                <label class="loginLabel" for="email">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    class="loginInput" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                @error('email')
                    <span class="loginError">{{ $message }}</span>
                @enderror
            </div>

            <div class="loginFormGroup">
                <label class="loginLabel" for="password">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    class="loginInput" 
                    required 
                    autocomplete="current-password" 
                />
                @error('password')
                    <span class="loginError">{{ $message }}</span>
                @enderror
            </div>

            <div class="loginActions">
                <a href="{{ route('register') }}" class="loginLink">Need an account?</a>
                <button type="submit" class="loginButton">Log in</button>
            </div>
        </form>
    </div>
</div>
@endsection
