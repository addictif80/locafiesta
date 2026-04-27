@extends('layouts.app')
@section('title', 'Réinitialiser le mot de passe')
@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-amber-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Nouveau mot de passe</h2>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border rounded-lg px-3 py-2 text-sm @error('password') border-red-400 @enderror">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-lg font-medium hover:bg-orange-600">
                Réinitialiser
            </button>
        </form>
    </div>
</div>
@endsection
