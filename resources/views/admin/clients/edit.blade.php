@extends('layouts.admin')

@section('title', 'Modifier — ' . $client->first_name . ' ' . $client->last_name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.clients.show', $client) }}" class="text-gray-400 hover:text-gray-600 transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="text-xl font-semibold text-gray-800">Modifier : {{ $client->first_name }} {{ $client->last_name }}</h2>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.clients.update', $client) }}">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 space-y-5">
            <h3 class="text-base font-semibold text-gray-700 pb-2 border-b border-gray-100">
                Informations personnelles
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $client->first_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('first_name') border-red-400 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $client->last_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('last_name') border-red-400 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                <input type="date" id="birth_date" name="birth_date"
                       value="{{ old('birth_date', $client->birth_date?->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('birth_date') border-red-400 @enderror">
                @error('birth_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $client->phone) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('phone') border-red-400 @enderror">
                @error('phone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <input type="text" id="address" name="address" value="{{ old('address', $client->address) }}"
                       placeholder="Numéro et nom de rue"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('address') border-red-400 @enderror">
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('postal_code') border-red-400 @enderror">
                    @error('postal_code')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $client->city) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none @error('city') border-red-400 @enderror">
                    @error('city')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition">
                <i class="fas fa-save mr-2"></i> Enregistrer les modifications
            </button>
            <a href="{{ route('admin.clients.show', $client) }}"
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-6 py-2.5 rounded-lg text-sm transition">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
