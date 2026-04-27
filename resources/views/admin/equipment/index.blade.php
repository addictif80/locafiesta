@extends('layouts.admin')

@section('title', 'Matériel')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Liste du matériel</h2>
    <a href="{{ route('admin.materiel.create') }}"
       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus"></i> Ajouter un matériel
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-16">Photo</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tarif / jour</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Caution</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Réservations</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($equipment as $e)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        @if($e->primaryPhoto)
                            <img src="{{ Storage::url($e->primaryPhoto->path) }}" alt="{{ $e->name }}"
                                 class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-xl"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $e->name }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $e->reference }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ number_format($e->daily_rate, 2, ',', ' ') }} €</td>
                    <td class="px-4 py-3 text-gray-700">{{ number_format($e->deposit_amount, 2, ',', ' ') }} €</td>
                    <td class="px-4 py-3">
                        @if($e->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-circle text-[8px]"></i> Actif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i> Inactif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $e->reservation_items_count ?? 0 }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.materiel.show', $e) }}"
                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.materiel.edit', $e) }}"
                               class="p-1.5 text-orange-500 hover:bg-orange-50 rounded transition" title="Modifier">
                                <i class="fas fa-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.materiel.destroy', $e) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Supprimer ce matériel ? Cette action est irréversible.')"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded transition" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-boxes-stacked text-4xl mb-3 block"></i>
                        Aucun matériel enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($equipment->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $equipment->links() }}
    </div>
    @endif
</div>
@endsection
