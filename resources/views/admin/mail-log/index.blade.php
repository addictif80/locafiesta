@extends('layouts.admin')
@section('title', 'Journalisation des mails')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Journalisation des mails</h2>
            <p class="text-sm text-gray-500 mt-0.5">Historique de tous les e-mails envoyés par l'application.</p>
        </div>
        <span class="text-sm text-gray-400">{{ $logs->total() }} e-mail(s) au total</span>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-4">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par destinataire, objet…"
                   class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600">
                <i class="fas fa-search mr-1"></i>Rechercher
            </button>
            @if(request('search'))
            <a href="{{ route('admin.mail-log.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @if($logs->isEmpty())
        <div class="px-6 py-16 text-center">
            <i class="fas fa-envelope-open text-5xl text-gray-200 mb-4"></i>
            <p class="text-gray-500">Aucun e-mail enregistré pour l'instant.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Destinataire</th>
                        <th class="px-5 py-3 text-left">Objet</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap text-gray-500">
                            {{ $log->sent_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-800">{{ $log->to_name ?: '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $log->to_email }}</div>
                        </td>
                        <td class="px-5 py-3 max-w-xs truncate text-gray-700">
                            {{ $log->subject }}
                        </td>
                        <td class="px-5 py-3">
                            @if($log->mailable_class)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $log->getMailableLabel() }}
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('admin.mail-log.show', $log) }}"
                                   class="text-orange-500 hover:text-orange-700 text-xs font-medium">
                                    <i class="fas fa-eye mr-1"></i>Aperçu
                                </a>
                                <form method="POST" action="{{ route('admin.mail-log.destroy', $log) }}"
                                      onsubmit="return confirm('Supprimer cette entrée ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
