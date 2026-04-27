@extends('layouts.client')
@section('title', 'Conditions Générales de Location')
@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">Conditions Générales de Location</h1>
    <div class="bg-white rounded-xl shadow-sm border p-8 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
        {{ \App\Models\Setting::get('cgv_text', 'Les conditions générales de location seront disponibles prochainement.') }}
    </div>
</div>
@endsection
