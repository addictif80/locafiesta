<div x-data="cookieBanner()" x-show="show" x-cloak
     class="fixed bottom-0 left-0 right-0 z-50 bg-gray-900 text-white px-6 py-4 shadow-2xl border-t border-gray-700">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-start gap-3 text-sm">
            <i class="fas fa-cookie-bite text-orange-400 text-lg mt-0.5 flex-shrink-0"></i>
            <p class="text-gray-300">
                Nous utilisons uniquement des cookies <strong class="text-white">strictement nécessaires</strong> au fonctionnement du site (session, sécurité).
                <a href="{{ route('privacy') }}" class="text-orange-400 hover:text-orange-300 underline ml-1">En savoir plus</a>
            </p>
        </div>
        <div class="flex gap-3 flex-shrink-0">
            <a href="{{ route('privacy') }}" class="px-4 py-2 text-sm text-gray-400 hover:text-white border border-gray-600 rounded-lg hover:border-gray-400 transition">
                En savoir plus
            </a>
            <button @click="accept()" class="px-5 py-2 text-sm font-medium bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
                J'accepte
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cookieBanner() {
    return {
        show: !localStorage.getItem('cookie_consent'),
        accept() {
            localStorage.setItem('cookie_consent', '1');
            this.show = false;
        }
    };
}
</script>
@endpush
