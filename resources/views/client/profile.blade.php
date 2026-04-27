@extends('layouts.client')

@section('title', 'Mon profil')

@section('content')
<div x-data="{ activeTab: '{{ old('_tab', 'info') }}' }" class="max-w-2xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="flex border-b">
            <button @click="activeTab = 'info'"
                    :class="activeTab === 'info' ? 'border-b-2 border-orange-500 text-orange-600 bg-orange-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-user"></i>
                <span class="hidden sm:inline">Mes informations</span>
            </button>
            <button @click="activeTab = 'password'"
                    :class="activeTab === 'password' ? 'border-b-2 border-orange-500 text-orange-600 bg-orange-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-lock"></i>
                <span class="hidden sm:inline">Mot de passe</span>
            </button>
            <button @click="activeTab = 'privacy'"
                    :class="activeTab === 'privacy' ? 'border-b-2 border-orange-500 text-orange-600 bg-orange-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-shield-halved"></i>
                <span class="hidden sm:inline">Confidentialité</span>
            </button>
        </div>

        {{-- Tab 1: Informations --}}
        <div x-show="activeTab === 'info'" x-cloak class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">Mes informations personnelles</h2>
            <form method="POST" action="{{ route('client.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="info">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('first_name') border-red-400 @enderror">
                        @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('last_name') border-red-400 @enderror">
                        @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance <span class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('birth_date') border-red-400 @enderror">
                        @error('birth_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('phone') border-red-400 @enderror">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse <span class="text-red-500">*</span></label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('address') border-red-400 @enderror">
                    @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('postal_code') border-red-400 @enderror">
                        @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville <span class="text-red-500">*</span></label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('city') border-red-400 @enderror">
                        @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">L'adresse email ne peut pas être modifiée ici.</p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab 2: Password --}}
        <div x-show="activeTab === 'password'" x-cloak class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">Changer le mot de passe</h2>
            <form method="POST" action="{{ route('client.profile.password') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="password">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('current_password') border-red-400 @enderror">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('password') border-red-400 @enderror">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 caractères.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
                        <i class="fas fa-lock"></i>
                        Mettre à jour le mot de passe
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab 3: Privacy --}}
        <div x-show="activeTab === 'privacy'" x-cloak class="p-6"
             x-data="{ showDeleteModal: false }">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">Confidentialité et données personnelles</h2>

            <div class="space-y-6">
                {{-- RGPD Consent Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-shield-halved text-blue-500 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-800 mb-1">Consentement RGPD</h3>
                            @if($user->rgpd_consent && $user->rgpd_consent_at)
                                <p class="text-sm text-blue-700">
                                    Vous avez donné votre consentement le
                                    <strong>{{ $user->rgpd_consent_at->format('d/m/Y à H:i') }}</strong>.
                                </p>
                            @else
                                <p class="text-sm text-blue-700">Aucun consentement enregistré.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Data Rights --}}
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <h3 class="text-sm font-semibold text-gray-800">Vos droits</h3>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>Droit d'accès à vos données personnelles</li>
                        <li>Droit de rectification</li>
                        <li>Droit à l'effacement ("droit à l'oubli")</li>
                        <li>Droit à la portabilité des données</li>
                    </ul>
                    <p class="text-xs text-gray-400 mt-2">
                        Pour toute demande, contactez-nous à <a href="mailto:privacy@locafiesta.fr" class="underline">privacy@locafiesta.fr</a>
                    </p>
                </div>

                {{-- Deletion request --}}
                <div class="border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-red-800 mb-2">
                        <i class="fas fa-trash mr-1"></i>Suppression de mon compte
                    </h3>
                    @if($user->deletion_requested_at)
                        <div class="bg-red-50 rounded p-3 text-sm text-red-700">
                            <i class="fas fa-clock mr-1"></i>
                            Une demande de suppression a été envoyée le
                            <strong>{{ $user->deletion_requested_at->format('d/m/Y') }}</strong>.
                            Nous la traiterons sous 30 jours.
                        </div>
                    @else
                        <p class="text-sm text-gray-600 mb-3">
                            Vous pouvez demander la suppression de votre compte et de toutes vos données personnelles.
                            Cette action est irréversible.
                        </p>
                        <button @click="showDeleteModal = true"
                                type="button"
                                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                            <i class="fas fa-trash"></i>
                            Demander la suppression de mon compte
                        </button>
                    @endif
                </div>
            </div>

            {{-- Delete Modal --}}
            <div x-show="showDeleteModal" x-cloak
                 class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                 @keydown.escape.window="showDeleteModal = false">
                <div @click.stop class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Confirmer la demande de suppression</h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Êtes-vous sûr de vouloir demander la suppression de votre compte ?
                            Toutes vos données seront définitivement effacées sous 30 jours.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <button @click="showDeleteModal = false" type="button"
                                class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            Annuler
                        </button>
                        <form method="POST" action="{{ route('client.profile.delete-request') }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition-colors text-sm">
                                Confirmer la suppression
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
