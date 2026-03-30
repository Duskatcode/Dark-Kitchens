@extends('layouts.app')

@section('title', 'Register')

@push('styles')
    @vite('resources/css/pages/register.css')
@endpush

@section('content')
<div class="registerPage">
    <div class="registerCard">
        <h1 class="registerTitle">Register</h1>

        <form method="POST" action="{{ route('register') }}" class="registerForm">
            @csrf

            <div class="registerFormGroup">
                <label class="registerLabel" for="name">Name</label>
                <input 
                    id="name" 
                    type="text" 
                    name="name" 
                    class="registerInput" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus 
                    autocomplete="given-name" 
                />
                @error('name')
                    <span class="registerError">{{ $message }}</span>
                @enderror
            </div>

            <div class="registerFormGroup">
                <label class="registerLabel" for="last_name">Last Name</label>
                <input 
                    id="last_name" 
                    type="text" 
                    name="last_name" 
                    class="registerInput" 
                    value="{{ old('last_name') }}" 
                    required 
                    autocomplete="family-name" 
                />
                @error('last_name')
                    <span class="registerError">{{ $message }}</span>
                @enderror
            </div>

            <div class="registerFormGroup">
                <label class="registerLabel" for="email">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    class="registerInput" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="username" 
                />
                @error('email')
                    <span class="registerError">{{ $message }}</span>
                @enderror
            </div>

            <div class="registerFormGroup">
                <label class="registerLabel" for="password">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    class="registerInput" 
                    required 
                    autocomplete="new-password" 
                />
                @error('password')
                    <span class="registerError">{{ $message }}</span>
                @enderror
            </div>

            <div class="registerFormGroup">
                <label class="registerLabel" for="password_confirmation">Confirm Password</label>
                <input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    class="registerInput" 
                    required 
                    autocomplete="new-password" 
                />
            </div>

            <div class="registerActions">
                <a href="{{ route('login') }}" class="registerLink">Already registered?</a>
                <button type="submit" class="registerButton">Register</button>
            </div>
        </form>
    </div>
</div>
@endsection
