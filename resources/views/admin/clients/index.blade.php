@extends('layouts.admin')

@section('title', 'Clients')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Clients</h2>
    <a href="{{ route('admin.clients.create') }}"
       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus"></i> Ajouter un client
    </a>
</div>

<!-- Filtres -->
<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher par nom, email, téléphone..."
                       class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
            </div>
        </div>
        <div>
            <select name="blacklist" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none bg-white">
                <option value="">Tous les clients</option>
                <option value="0" {{ request('blacklist') === '0' ? 'selected' : '' }}>Non blacklistés</option>
                <option value="1" {{ request('blacklist') === '1' ? 'selected' : '' }}>Liste noire uniquement</option>
            </select>
        </div>
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-filter mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['search', 'blacklist']))
        <a href="{{ route('admin.clients.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition text-center">
            <i class="fas fa-times mr-1"></i> Réinitialiser
        </a>
        @endif
    </form>
</div>

<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Inscription</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Réservations</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clients as $client)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-semibold text-xs flex-shrink-0">
                                {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $client->first_name }} {{ $client->last_name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $client->email }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $client->phone ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $client->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $client->reservations_count ?? 0 }}</td>
                    <td class="px-4 py-3">
                        @if($client->is_blacklisted)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-ban text-[8px]"></i> Liste noire
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-circle text-[8px]"></i> Actif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.clients.show', $client) }}"
                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.clients.edit', $client) }}"
                               class="p-1.5 text-orange-500 hover:bg-orange-50 rounded transition" title="Modifier">
                                <i class="fas fa-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Supprimer ce client ? Cette action est irréversible.')"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded transition" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-users text-4xl mb-3 block"></i>
                        Aucun client trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $clients->links() }}
    </div>
    @endif
</div>
@endsection
