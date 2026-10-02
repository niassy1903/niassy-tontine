@extends('layouts.app')
@php($pageTitle = 'Mon profil')
@section('content')
<div class="page-heading"><div><span class="eyebrow blue">ESPACE PERSONNEL</span><h1>Votre profil</h1><p>Gardez vos informations et votre sécurité à jour.</p></div><span class="badge-soft success"><i class="bi bi-shield-check me-1"></i>Compte protégé</span></div>
<div class="row g-4">
    <div class="col-xl-8">
        <div class="panel-card">
            <div class="d-flex align-items-center gap-3 mb-4"><span class="avatar lg">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><div><h3 class="panel-title mb-1">Informations personnelles</h3><span class="panel-subtitle">Ces informations sont visibles par les membres de vos tontines.</span></div></div>
            <form method="POST" action="{{ route('profile.update') }}" class="row g-4">@csrf @method('PUT')
                <div class="col-md-6"><label class="form-label">Nom complet</label><input class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required></div>
                <div class="col-md-6"><label class="form-label">Adresse email</label><input class="form-control" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required></div>
                <div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+221 77 000 00 00"></div>
                <div class="col-md-6"><label class="form-label">Adresse</label><input class="form-control" name="address" value="{{ old('address', auth()->user()->address) }}" placeholder="Dakar, Sénégal"></div>
                <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary"><i class="bi bi-check2 me-2"></i>Enregistrer le profil</button></div>
            </form>
        </div>
    </div>
    <div class="col-xl-4"><div class="panel-card h-100 bg-[#0b3d91] text-white"><span class="pill light">Votre identité</span><h3 class="mt-4 text-white">Un espace clair pour chaque membre.</h3><p class="mt-3 text-white-50">Votre nom et votre rôle permettent à la communauté de savoir qui agit sur les cotisations et les décisions.</p><div class="mt-5 d-flex align-items-center gap-2 small text-white-50"><i class="bi bi-info-circle"></i> Dernière connexion : {{ auth()->user()->last_login_at?->diffForHumans() ?: 'Première connexion' }}</div></div></div>
</div>
<div class="row g-4 mt-1">
    <div class="col-xl-8"><div class="panel-card"><div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="panel-title">Changer le mot de passe</h3><span class="panel-subtitle">Utilisez au moins 12 caractères et évitez les mots faciles à deviner.</span></div><i class="bi bi-key text-primary fs-4"></i></div><form method="POST" action="{{ route('profile.password') }}" class="row g-4">@csrf @method('PUT')<div class="col-12"><label class="form-label">Mot de passe actuel</label><input class="form-control" type="password" name="current_password" required autocomplete="current-password"></div><div class="col-md-6"><label class="form-label">Nouveau mot de passe</label><input class="form-control" type="password" name="password" required minlength="12" autocomplete="new-password"></div><div class="col-md-6"><label class="form-label">Confirmer le nouveau mot de passe</label><input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password"></div><div class="col-12 d-flex justify-content-end"><button class="btn btn-light"><i class="bi bi-lock me-2"></i>Mettre à jour</button></div></form></div></div>
</div>
@endsection