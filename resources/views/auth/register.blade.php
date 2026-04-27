@extends('layouts.app')
@section('title', 'Inscription')
@section('body')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-100 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <a href="{{ route('login') }}" class="text-3xl font-bold text-orange-500"><i class="fas fa-party-horn"></i> LocaFiesta</a>
            <p class="text-gray-500 mt-2">Créez votre compte pour réserver</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Créer un compte</h2>
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm @error('first_name') border-red-400 @enderror">
                        @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm @error('last_name') border-red-400 @enderror">
                        @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm @error('birth_date') border-red-400 @enderror">
                    @error('birth_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse postale *</label>
                    <input type="text" name="address" value="{{ old('address') }}" required placeholder="Numéro et rue"
                           class="w-full border rounded-lg px-3 py-2 text-sm @error('address') border-red-400 @enderror">
                    @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required maxlength="10"
                               class="w-full border rounded-lg px-3 py-2 text-sm @error('postal_code') border-red-400 @enderror">
                        @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm @error('city') border-red-400 @enderror">
                        @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full border rounded-lg px-3 py-2 text-sm @error('password') border-red-400 @enderror">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="rgpd_consent" value="1" {{ old('rgpd_consent') ? 'checked' : '' }}
                               required class="mt-0.5 rounded @error('rgpd_consent') border-red-400 @enderror">
                        <span class="text-sm text-gray-600">
                            J'accepte la <a href="{{ route('privacy') }}" target="_blank" class="text-orange-500 hover:underline">politique de confidentialité</a>
                            et les <a href="{{ route('cgv') }}" target="_blank" class="text-orange-500 hover:underline">conditions générales de location</a>. *
                        </span>
                    </label>
                    @error('rgpd_consent')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-lg font-medium hover:bg-orange-600 transition">
                    Créer mon compte
                </button>
            </form>
            <p class="text-center text-sm text-gray-500 mt-6">
                Déjà un compte ? <a href="{{ route('login') }}" class="text-orange-500 font-medium hover:underline">Se connecter</a>
            </p>
        </div>
    </div>
</div>
@endsection
