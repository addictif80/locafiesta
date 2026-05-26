@extends('layouts.client')
@section('title', 'Nouvelle réservation')
@push('head')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
@endpush
@section('content')
<div x-data="reservationWizard()" class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Nouvelle réservation</h1>
        <div class="flex items-center gap-4 mt-4">
            <template x-for="(stepName, i) in steps" :key="i">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                         :class="currentStep > i+1 ? 'bg-green-500 text-white' : (currentStep === i+1 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500')">
                        <span x-show="currentStep <= i+1" x-text="i+1"></span>
                        <span x-show="currentStep > i+1"><i class="fas fa-check text-xs"></i></span>
                    </div>
                    <span class="text-sm" :class="currentStep === i+1 ? 'font-semibold text-gray-800' : 'text-gray-400'" x-text="stepName"></span>
                    <i x-show="i < steps.length - 1" class="fas fa-chevron-right text-gray-300 text-xs ml-2"></i>
                </div>
            </template>
        </div>
    </div>

    <form method="POST" action="{{ route('client.reservations.store') }}" id="reservationForm" @submit="submitForm">
        @csrf
        <input type="hidden" name="start_date" x-model="form.start_date">
        <input type="hidden" name="end_date" x-model="form.end_date">
        <input type="hidden" name="start_time" x-model="form.start_time">
        <input type="hidden" name="end_time" x-model="form.end_time">
        <input type="hidden" name="promo_code" x-model="form.promo_code">
        <input type="hidden" name="use_different_address" :value="form.use_different_address ? 1 : 0">
        <input type="hidden" name="use_address" x-model="form.use_address">
        <input type="hidden" name="use_postal_code" x-model="form.use_postal_code">
        <input type="hidden" name="use_city" x-model="form.use_city">
        <template x-for="id in form.equipment_ids" :key="id">
            <input type="hidden" name="equipment_ids[]" :value="id">
        </template>

        {{-- Étape 1: Matériel & Dates --}}
        <div x-show="currentStep === 1" class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Sélectionnez le matériel</h3>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($equipment as $equip)
                    <div @click="toggleEquipment({{ $equip->id }}, {{ $equip->daily_rate }})"
                         :class="form.equipment_ids.includes({{ $equip->id }}) ? 'border-orange-400 bg-orange-50 ring-2 ring-orange-200' : 'border-gray-200 hover:border-orange-200'"
                         class="border-2 rounded-xl p-4 cursor-pointer transition-all">
                        @if($equip->primaryPhoto)
                        <img src="{{ asset('storage/' . $equip->primaryPhoto->path) }}" alt="{{ $equip->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                        @else
                        <div class="w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                            <i class="fas fa-box text-gray-300 text-3xl"></i>
                        </div>
                        @endif
                        <h4 class="font-semibold text-sm text-gray-800">{{ $equip->name }}</h4>
                        <p class="text-xs text-gray-400 mb-2">{{ $equip->reference }}</p>
                        <p class="text-orange-500 font-bold text-sm">{{ number_format($equip->daily_rate, 2, ',', ' ') }} €/jour</p>
                        <div class="mt-2 flex items-center justify-end">
                            <span :class="form.equipment_ids.includes({{ $equip->id }}) ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-400'"
                                  class="w-6 h-6 rounded-full flex items-center justify-center text-xs">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p x-show="form.equipment_ids.length === 0" class="text-red-500 text-xs mt-2">Sélectionnez au moins un matériel</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Sélectionnez les dates</h3>
                <div x-show="form.equipment_ids.length === 0" class="text-sm text-gray-400 text-center py-8">
                    Sélectionnez d'abord du matériel pour voir les disponibilités.
                </div>
                <div x-show="form.equipment_ids.length > 0">
                    <div id="calendar" class="mb-4"></div>
                    <div x-show="form.start_date && !form.end_date" class="text-sm text-orange-600 mb-3">
                        <i class="fas fa-mouse-pointer mr-1"></i> Cliquez maintenant sur la date de retour
                    </div>
                    <div x-show="!form.start_date" class="text-sm text-gray-400 mb-3">
                        <i class="fas fa-mouse-pointer mr-1"></i> Cliquez sur la date de départ
                    </div>
                    <div x-show="form.start_date && form.end_date" class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-orange-700">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Période sélectionnée : <strong x-text="formatDate(form.start_date)"></strong> → <strong x-text="formatDate(form.end_date)"></strong>
                            (<span x-text="daysCount"></span> jour(s))
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure de départ *</label>
                            <select x-model="form.start_time" class="w-full border rounded-lg px-3 py-2 text-sm">
                                @for($h = 8; $h <= 20; $h++)
                                <option value="{{ sprintf('%02d:00:00', $h) }}">{{ sprintf('%02dh00', $h) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure de retour *</label>
                            <select x-model="form.end_time" class="w-full border rounded-lg px-3 py-2 text-sm">
                                @for($h = 8; $h <= 20; $h++)
                                <option value="{{ sprintf('%02d:00:00', $h) }}" {{ $h === 18 ? 'selected' : '' }}>{{ sprintf('%02dh00', $h) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="nextStep"
                        :disabled="form.equipment_ids.length === 0 || !form.start_date || !form.end_date"
                        class="bg-orange-500 text-white px-8 py-3 rounded-xl font-medium hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Suivant <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        {{-- Étape 2: Récapitulatif & Options --}}
        <div x-show="currentStep === 2" x-cloak class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Récapitulatif</h3>
                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr class="text-gray-500">
                            <th class="text-left pb-2">Matériel</th>
                            <th class="text-right pb-2">Tarif/j</th>
                            <th class="text-right pb-2">Jours</th>
                            <th class="text-right pb-2">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <template x-for="item in cartItems" :key="item.id">
                            <tr>
                                <td class="py-2 font-medium" x-text="item.name"></td>
                                <td class="py-2 text-right" x-text="formatPrice(item.price) + ' €'"></td>
                                <td class="py-2 text-right" x-text="daysCount"></td>
                                <td class="py-2 text-right font-bold" x-text="formatPrice(item.price * daysCount) + ' €'"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="border-t">
                        <tr>
                            <td colspan="3" class="pt-3 text-gray-500">Sous-total</td>
                            <td class="pt-3 text-right font-bold" x-text="formatPrice(subtotal) + ' €'"></td>
                        </tr>
                        <template x-if="discountAmount > 0">
                            <tr class="text-green-600">
                                <td colspan="3" class="pt-1">Remise code promo</td>
                                <td class="pt-1 text-right font-bold" x-text="'-' + formatPrice(discountAmount) + ' €'"></td>
                            </tr>
                        </template>
                        <tr class="text-lg font-bold border-t">
                            <td colspan="3" class="pt-3">Total</td>
                            <td class="pt-3 text-right text-orange-600" x-text="formatPrice(total) + ' €'"></td>
                        </tr>
                        <tr class="text-blue-600">
                            <td colspan="3" class="pt-1 text-sm">Acompte à payer ({{ \App\Models\Setting::get('deposit_percentage', 30) }}%)</td>
                            <td class="pt-1 text-right font-bold text-sm" x-text="formatPrice(depositAmount) + ' €'"></td>
                        </tr>
                        <tr class="text-gray-500">
                            <td colspan="3" class="pt-1 text-sm">Solde restant</td>
                            <td class="pt-1 text-right text-sm" x-text="formatPrice(balanceAmount) + ' €'"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Code promo</h3>
                <div class="flex gap-3">
                    <input type="text" x-model="promo.code" placeholder="Entrez votre code promo" class="flex-1 border rounded-lg px-3 py-2 text-sm uppercase">
                    <button type="button" @click="applyPromoCode()" :disabled="promo.loading" class="bg-gray-800 text-white px-5 py-2 rounded-lg text-sm hover:bg-gray-700 disabled:opacity-50">
                        <span x-show="!promo.loading">Appliquer</span>
                        <span x-show="promo.loading"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
                <p x-show="promo.error" x-text="promo.error" class="text-red-500 text-xs mt-2"></p>
                <div x-show="promo.applied" class="mt-2 bg-green-50 border border-green-200 rounded-lg p-2 text-sm text-green-700 flex items-center gap-2">
                    <i class="fas fa-tag"></i> Code appliqué : <strong x-text="promo.code"></strong>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" x-model="form.use_different_address" class="rounded">
                    <span class="font-medium text-gray-800">Adresse d'utilisation différente de mon adresse de facturation</span>
                </label>
                <div x-show="form.use_different_address" x-cloak class="mt-4 grid grid-cols-3 gap-4">
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse d'utilisation *</label>
                        <input type="text" x-model="form.use_address" placeholder="N° et rue" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                        <input type="text" x-model="form.use_postal_code" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                        <input type="text" x-model="form.use_city" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="prevStep" class="border px-6 py-2.5 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </button>
                <button type="button" @click="nextStep" class="bg-orange-500 text-white px-8 py-3 rounded-xl font-medium hover:bg-orange-600">
                    Vérifier et payer <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        {{-- Étape 3: Confirmation --}}
        <div x-show="currentStep === 3" x-cloak>
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-check-circle text-green-500 mr-2"></i>Résumé de votre réservation</h3>
                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-medium text-gray-500 mb-2 uppercase text-xs tracking-wide">Période</h4>
                        <p class="font-semibold" x-text="formatDate(form.start_date) + ' (' + form.start_time.substring(0,5) + ') → ' + formatDate(form.end_date) + ' (' + form.end_time.substring(0,5) + ')'"></p>
                        <p class="text-gray-500 mt-0.5" x-text="daysCount + ' jour(s)'"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-500 mb-2 uppercase text-xs tracking-wide">Matériel</h4>
                        <ul class="space-y-1">
                            <template x-for="item in cartItems" :key="item.id">
                                <li x-text="'• ' + item.name"></li>
                            </template>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-500 mb-2 uppercase text-xs tracking-wide">Paiement</h4>
                        <p>Total: <strong x-text="formatPrice(total) + ' €'"></strong></p>
                        <p class="text-blue-600 font-bold mt-1">Acompte à payer maintenant: <span x-text="formatPrice(depositAmount) + ' €'"></span></p>
                        <p class="text-gray-500">Solde sur place: <span x-text="formatPrice(balanceAmount) + ' €'"></span></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-500 mb-2 uppercase text-xs tracking-wide">Votre adresse</h4>
                        <p>{{ auth()->user()->address }}</p>
                        <p>{{ auth()->user()->postal_code }} {{ auth()->user()->city }}</p>
                        <template x-if="form.use_different_address">
                            <div class="mt-2 text-orange-600">
                                <p class="text-xs font-medium">Adresse d'utilisation:</p>
                                <p x-text="form.use_address + ', ' + form.use_postal_code + ' ' + form.use_city"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Conditions d'annulation :</strong> Annulation gratuite jusqu'à 48h avant le début de la location. Passé ce délai, l'acompte est conservé.
            </div>
            <div class="flex justify-between">
                <button type="button" @click="prevStep" class="border px-6 py-2.5 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Modifier
                </button>
                <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-xl font-medium hover:bg-orange-600 text-lg">
                    <i class="fas fa-lock mr-2"></i>Confirmer et payer l'acompte
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
const equipmentData = @json($equipment->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'price' => (float)$e->daily_rate]));
const depositPercentage = {{ \App\Models\Setting::get('deposit_percentage', 30) }};
let calendar = null;

function toYMD(date) {
    return date.getFullYear() + '-' +
        String(date.getMonth() + 1).padStart(2, '0') + '-' +
        String(date.getDate()).padStart(2, '0');
}

function reservationWizard() {
    return {
        currentStep: 1,
        steps: ['Matériel & dates', 'Options', 'Confirmation'],
        form: {
            equipment_ids: [],
            start_date: '',
            end_date: '',
            start_time: '09:00:00',
            end_time: '18:00:00',
            promo_code: '',
            use_different_address: false,
            use_address: '',
            use_postal_code: '',
            use_city: '',
        },
        discountAmount: 0,
        promo: { code: '', loading: false, applied: false, error: '' },
        get cartItems() {
            return equipmentData.filter(e => this.form.equipment_ids.includes(e.id));
        },
        get daysCount() {
            if (!this.form.start_date || !this.form.end_date) return 0;
            const start = new Date(this.form.start_date + 'T' + (this.form.start_time || '09:00:00'));
            const end   = new Date(this.form.end_date   + 'T' + (this.form.end_time   || '18:00:00'));
            const minutes = (end - start) / 60000;
            return Math.max(1, Math.ceil(minutes / 1440));
        },
        get subtotal() {
            return this.cartItems.reduce((sum, item) => sum + item.price * this.daysCount, 0);
        },
        get total() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },
        get depositAmount() {
            return Math.round(this.total * depositPercentage / 100 * 100) / 100;
        },
        get balanceAmount() {
            return Math.round((this.total - this.depositAmount) * 100) / 100;
        },
        toggleEquipment(id, price) {
            const idx = this.form.equipment_ids.indexOf(id);
            if (idx === -1) this.form.equipment_ids.push(id);
            else this.form.equipment_ids.splice(idx, 1);
            if (this.form.equipment_ids.length > 0 && !calendar) {
                this.$nextTick(() => this.initCalendar());
            } else {
                this.refreshCalendar();
            }
        },
        formatDate(d) {
            if (!d) return '';
            const [y, m, day] = d.split('-');
            return `${day}/${m}/${y}`;
        },
        formatPrice(n) {
            return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(n);
        },
        renderSelectionEvent() {
            calendar.getEventById('sel')?.remove();
            const start = this.form.start_date;
            if (!start) return;
            const end = this.form.end_date || start;
            const endExcl = new Date(end + 'T12:00:00');
            endExcl.setDate(endExcl.getDate() + 1);
            calendar.addEvent({ id: 'sel', start, end: toYMD(endExcl), allDay: true, display: 'background', color: '#f97316' });
        },
        async refreshCalendar() {
            if (this.form.equipment_ids.length === 0) {
                if (calendar) calendar.removeAllEventSources();
                return;
            }
            const resp = await fetch('{{ route('client.availability') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ equipment_ids: this.form.equipment_ids })
            });
            const unavailable = await resp.json();
            if (calendar) {
                calendar.removeAllEventSources();
                calendar.addEventSource(unavailable.map(d => ({ start: d, allDay: true, display: 'background', color: '#ef4444' })));
                this.renderSelectionEvent();
            }
        },
        nextStep() {
            if (this.currentStep === 1 && (this.form.equipment_ids.length === 0 || !this.form.start_date || !this.form.end_date)) return;
            this.currentStep++;
        },
        prevStep() { this.currentStep--; },
        initCalendar() {
            const self = this;
            const todayStr = toYMD(new Date());
            calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'fr',
                selectable: false,
                validRange: { start: todayStr },
                dateClick(info) {
                    const d = info.dateStr;
                    if (!self.form.start_date || self.form.end_date) {
                        // Premier clic : début de sélection
                        self.form.start_date = d;
                        self.form.end_date = '';
                    } else {
                        // Deuxième clic : fin de sélection
                        if (d < self.form.start_date) {
                            self.form.end_date = self.form.start_date;
                            self.form.start_date = d;
                        } else {
                            self.form.end_date = d;
                        }
                    }
                    self.renderSelectionEvent();
                },
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                height: 'auto',
            });
            calendar.render();
            this.refreshCalendar();
        },
        async applyPromoCode() {
            if (!this.promo.code) return;
            this.promo.loading = true;
            this.promo.error = '';
            const resp = await fetch('{{ route('client.promo.check') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ code: this.promo.code })
            });
            const data = await resp.json();
            this.promo.loading = false;
            if (data.valid) {
                this.promo.applied = true;
                this.form.promo_code = this.promo.code;
                if (data.type === 'percentage') {
                    this.discountAmount = Math.round(this.subtotal * data.value / 100 * 100) / 100;
                } else {
                    this.discountAmount = Math.min(Number(data.value), this.subtotal);
                }
            } else {
                this.promo.error = data.message;
            }
        },
        submitForm(e) {
            if (this.form.equipment_ids.length === 0 || !this.form.start_date || !this.form.end_date) {
                e.preventDefault();
                alert('Veuillez sélectionner du matériel et des dates.');
            }
        },
    };
}

</script>
@endpush
