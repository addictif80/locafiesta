@extends('layouts.admin')
@section('title', 'Aperçu e-mail')
@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-5 flex items-start justify-between">
        <div>
            <a href="{{ route('admin.mail-log.index') }}"
               class="text-sm text-orange-500 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i>Journalisation des mails
            </a>
            <h2 class="text-xl font-bold text-gray-800 mt-2">Aperçu e-mail</h2>
        </div>
        <form method="POST" action="{{ route('admin.mail-log.destroy', $mailLog) }}"
              onsubmit="return confirm('Supprimer cette entrée ?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm hover:bg-red-100">
                <i class="fas fa-trash"></i>Supprimer
            </button>
        </form>
    </div>

    {{-- Meta card --}}
    <div class="bg-white rounded-xl shadow-sm border mb-5">
        <div class="px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-800 text-sm">Informations</h3>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Date d'envoi :</span>
                <span class="font-medium ml-1">{{ $mailLog->sent_at->format('d/m/Y à H:i:s') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Destinataire :</span>
                <span class="font-medium ml-1">
                    {{ $mailLog->to_name ? $mailLog->to_name . ' &lt;' . $mailLog->to_email . '&gt;' : $mailLog->to_email }}
                </span>
            </div>
            <div class="col-span-2">
                <span class="text-gray-500">Objet :</span>
                <span class="font-semibold ml-1 text-gray-800">{{ $mailLog->subject }}</span>
            </div>
            <div>
                <span class="text-gray-500">Type :</span>
                @if($mailLog->mailable_class)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 ml-1">
                    {{ $mailLog->getMailableLabel() }}
                </span>
                <span class="text-xs text-gray-400 ml-1">{{ $mailLog->mailable_class }}</span>
                @else
                <span class="text-gray-400 ml-1">—</span>
                @endif
            </div>
        </div>
    </div>

    {{-- HTML preview --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-envelope-open-text mr-2 text-orange-400"></i>Aperçu HTML
            </h3>
            <a href="{{ route('admin.mail-log.preview', $mailLog) }}" target="_blank"
               class="text-xs text-gray-500 hover:text-orange-500 flex items-center gap-1">
                <i class="fas fa-external-link-alt"></i> Ouvrir dans un onglet
            </a>
        </div>
        @if($mailLog->html_body)
        <iframe src="{{ route('admin.mail-log.preview', $mailLog) }}"
                class="w-full border-0"
                style="height:700px;"
                sandbox="allow-same-origin">
        </iframe>
        @else
        <div class="px-5 py-10 text-center text-gray-400">
            <i class="fas fa-code text-3xl mb-3"></i>
            <p class="text-sm">Aucun contenu HTML disponible pour cet e-mail.</p>
        </div>
        @endif
    </div>

</div>
@endsection
