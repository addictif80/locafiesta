@extends('layouts.admin')
@section('title', 'Équipe')
@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Gestion de l'équipe</h2>
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Ajouter un membre</h3>
                <form method="POST" action="{{ route('admin.team.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        @error('first_name')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        @error('email')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Rôle *</label>
                        <select name="role" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mot de passe *</label>
                        <input type="password" name="password" required minlength="8" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @error('password')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" required placeholder="Confirmer" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg text-sm font-medium hover:bg-orange-600">Créer le compte</button>
                </form>
            </div>
        </div>
        <div class="col-span-2">
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-800">Membres de l'équipe</h3></div>
                <div class="divide-y">
                    @foreach($team as $member)
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-800">{{ $member->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $member->email }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $member->role === 'admin' ? 'Administrateur' : 'Agent' }}
                            </span>
                            @if($member->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.team.destroy', $member) }}" onsubmit="return confirm('Supprimer ce membre ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-sm"><i class="fas fa-trash"></i></button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400">(vous)</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
