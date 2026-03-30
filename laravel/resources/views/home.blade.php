@extends('layouts.app')

@section('title', 'Welcome to Dark Kitchens')

@push('styles')
    @vite('resources/css/pages/home.css')
@endpush

@section('content')
<div class="homePage">
    <div class="homeCard">
        <h1 class="homeTitle">Dark Kitchens</h1>
        <p class="homeSubtitle">Your premium dark kitchen experience.</p>
        
        <div class="homeActions">
            @auth
                <a href="{{ route('dashboard') }}" class="homeButton homeButtonPrimary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="homeButton homeButtonOutline">Log in</a>
                <a href="{{ route('register') }}" class="homeButton homeButtonPrimary">Register</a>
            @endauth
        </div>
    </div>
</div>
@endsection
