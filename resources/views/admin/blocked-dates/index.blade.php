@extends('layouts.admin')
@section('title', 'Blocages de dates')
@push('head')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
@endpush
@section('content')
<div class="max-w-5xl mx-auto">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Blocages de dates</h2>
    <div class="grid grid-cols-3 gap-6">
        <div>
            <div class="bg-white rounded-xl shadow-sm border p-5 mb-4">
                <h3 class="font-semibold text-gray-800 mb-4">Ajouter un blocage</h3>
                <form method="POST" action="{{ route('admin.blocked-dates.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                        <select name="type" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="holiday">Congés</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="closure">Fermeture</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date de début *</label>
                        <input type="date" name="start_date" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        @error('start_date')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date de fin *</label>
                        <input type="date" name="end_date" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        @error('end_date')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Raison</label>
                        <input type="text" name="reason" placeholder="Ex: Vacances d'été" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg text-sm font-medium hover:bg-orange-600">Ajouter le blocage</button>
                </form>
            </div>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-4 py-3 border-b"><h4 class="font-medium text-gray-800 text-sm">Blocages actifs</h4></div>
                <div class="divide-y max-h-80 overflow-y-auto">
                    @forelse($blockedDates as $block)
                    <div class="px-4 py-3 flex items-start justify-between">
                        <div>
                            <span class="text-xs font-medium text-gray-800">{{ $block->start_date->format('d/m/Y') }} → {{ $block->end_date->format('d/m/Y') }}</span>
                            <p class="text-xs text-gray-500">{{ $block->type_label }}{{ $block->reason ? ' · '.$block->reason : '' }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.blocked-dates.destroy', $block) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    @empty
                    <div class="px-4 py-6 text-center text-xs text-gray-400">Aucun blocage</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="bg-white rounded-xl shadow-sm border p-4">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek' },
        height: 'auto',
        events: '{{ route('admin.blocked-dates.json') }}',
        eventColor: '#ef4444',
    });
    calendar.render();
});
</script>
@endpush
