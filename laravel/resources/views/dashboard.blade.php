@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    @vite('resources/css/pages/dashboard.css')
@endpush

@section('content')
<div class="dashboardPage">
    <header class="dashboardHeader">
        <h2 class="dashboardTitle">Dashboard</h2>
        <div class="dashboardUserInfo">
            <span class="dashboardUserName">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dashboardLogoutButton">Log out</button>
            </form>
        </div>
    </header>

    <main class="dashboardContent">
        <div class="dashboardCard">
            <p class="dashboardCardText">Welcome back, {{ Auth::user()->name }}! You are logged in.</p>
        </div>
    </main>
</div>
@endsection
