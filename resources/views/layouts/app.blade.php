<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Niassy Tontine' }} · Niassy Tontine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/niassy.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
<div class="app-shell min-h-screen">
    @auth
        <div class="fixed inset-0 z-10 hidden bg-slate-950/40 backdrop-blur-sm lg:hidden" data-sidebar-overlay></div>
        <aside class="sidebar w-64 border-r border-white/10 bg-[#0b3d91] shadow-2xl shadow-blue-950/20" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">N</span><span>niassy<span class="brand-light">tontine</span></span></a>
            <div class="sidebar-caption">ESPACE DE GESTION</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i>Vue d’ensemble</a>
                <a class="nav-link {{ request()->routeIs('tontines.*') ? 'active' : '' }}" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Mes tontines</a>
                <a class="nav-link {{ request()->routeIs('contributions.*') ? 'active' : '' }}" href="{{ route('contributions.index') }}"><i class="bi bi-calendar2-check-fill"></i>Cotisations</a>
                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}"><i class="bi bi-wallet2"></i>Paiements</a>
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><i class="bi bi-receipt"></i>Dépenses</a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ auth()->user()->tontines->first() ? route('reports.index', auth()->user()->tontines->first()) : route('tontines.create') }}"><i class="bi bi-bar-chart-fill"></i>Rapports</a>
                <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ auth()->user()->tontines->first() ? route('messages.index', auth()->user()->tontines->first()) : route('tontines.create') }}"><i class="bi bi-chat-square-text-fill"></i>Communauté</a>
                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}"><i class="bi bi-bell-fill"></i>Notifications @if(auth()->user()->unreadNotifications()->count())<span class="badge rounded-pill bg-primary ms-auto">{{ auth()->user()->unreadNotifications()->count() }}</span>@endif</a>
            </nav>
            <div class="sidebar-caption mt-4">MON COMPTE</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon profil</a>
                @if(auth()->user()->isSuperAdmin())
                    <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock-fill"></i>Backoffice</a>
                @endif
            </nav>
            <div class="sidebar-bottom">
                <div class="user-chip"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="text-truncate"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span></div>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-link logout-btn"><i class="bi bi-box-arrow-right"></i>Déconnexion</button></form>
            </div>
        </aside>
    @endauth
        <main class="{{ auth()->check() ? 'main-area min-h-screen bg-[#f7faff]' : '' }}">
        @auth
            <header class="topbar sticky top-0 z-10 border-slate-200/80 bg-white/90 shadow-sm backdrop-blur-xl">
                <button class="btn mobile-toggle d-lg-none rounded-xl" data-toggle-sidebar><i class="bi bi-list"></i></button>
                <div class="breadcrumb-area"><span class="eyebrow">NIASSY TONTINE</span><span class="topbar-title">{{ $pageTitle ?? 'Votre espace' }}</span></div>
                <div class="topbar-actions"><a class="icon-btn" href="{{ route('tontines.public') }}" title="Explorer"><i class="bi bi-compass"></i></a><a class="icon-btn notification-dot" href="{{ route('notifications.index') }}" title="Notifications"><i class="bi bi-bell"></i>@if(auth()->user()->unreadNotifications()->count())<b></b>@endif</a></div>
            </header>
        @endauth
        <div class="{{ auth()->check() ? 'content-area' : '' }}">
            @if(session('success')) <div class="alert alert-success flash rounded-2xl border border-emerald-100 bg-emerald-50 text-emerald-800" role="alert"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="float-end border-0 bg-transparent" data-dismiss-alert><i class="bi bi-x-lg"></i></button></div> @endif
            @if($errors->any()) <div class="alert alert-danger flash rounded-2xl border border-red-100 bg-red-50 text-red-800" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}<button type="button" class="float-end border-0 bg-transparent" data-dismiss-alert><i class="bi bi-x-lg"></i></button></div> @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>