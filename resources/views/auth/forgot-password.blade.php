@extends('layouts.app')
@section('title', 'Mot de passe oublié')
@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-amber-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Mot de passe oublié</h2>
        <p class="text-sm text-gray-500 mb-6">Saisissez votre email pour recevoir un lien de réinitialisation.</p>
        @if(session('status'))<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg p-3 text-sm">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="Votre adresse email"
                   class="w-full border rounded-lg px-3 py-2 text-sm @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-lg font-medium hover:bg-orange-600">
                Envoyer le lien
            </button>
        </form>
        <a href="{{ route('login') }}" class="block text-center text-sm text-orange-500 mt-4 hover:underline">Retour à la connexion</a>
    </div>
</div>
@endsection
