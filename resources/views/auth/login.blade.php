@extends('layouts.app')
@section('title', 'Connexion')
@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-amber-100 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-orange-500"><i class="fas fa-party-horn"></i> LocaFiesta</h1>
            <p class="text-gray-500 mt-2">Location de matériel de fête</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Connexion</h2>
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded"> Se souvenir de moi
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-orange-500 hover:underline">Mot de passe oublié ?</a>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-lg font-medium hover:bg-orange-600 transition">
                    Se connecter
                </button>
            </form>
            <p class="text-center text-sm text-gray-500 mt-6">
                Pas encore de compte ? <a href="{{ route('register') }}" class="text-orange-500 font-medium hover:underline">Créer un compte</a>
            </p>
        </div>
    </div>
</div>
@endsection
