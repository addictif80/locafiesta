<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Client;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/connexion', [LoginController::class, 'login']);
    Route::get('/inscription', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'register']);
    Route::get('/mot-de-passe-oublie', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/deconnexion', [LoginController::class, 'logout'])->name('logout');

// RGPD
Route::view('/politique-de-confidentialite', 'legal.privacy')->name('privacy');
Route::view('/mentions-legales', 'legal.mentions')->name('mentions');
Route::view('/conditions-generales', 'legal.cgv')->name('cgv');

// Admin panel
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Equipment
    Route::resource('materiel', Admin\EquipmentController::class)->parameters(['materiel' => 'equipment']);
    Route::delete('materiel/photo/{photo}', [Admin\EquipmentController::class, 'deletePhoto'])->name('equipment.photo.delete');
    Route::post('materiel/photo/{photo}/primary', [Admin\EquipmentController::class, 'setPrimaryPhoto'])->name('equipment.photo.primary');
    Route::post('materiel/{equipment}/checklist', [Admin\EquipmentController::class, 'storeChecklistItem'])->name('equipment.checklist.store');
    Route::delete('materiel/checklist/{item}', [Admin\EquipmentController::class, 'destroyChecklistItem'])->name('equipment.checklist.destroy');
    Route::post('materiel/{equipment}/bareme', [Admin\EquipmentController::class, 'storeDamageScaleItem'])->name('equipment.damage-scale.store');
    Route::delete('materiel/bareme/{item}', [Admin\EquipmentController::class, 'destroyDamageScaleItem'])->name('equipment.damage-scale.destroy');

    // Clients
    Route::resource('clients', Admin\ClientController::class);
    Route::post('clients/{client}/blacklist', [Admin\ClientController::class, 'toggleBlacklist'])->name('clients.blacklist');
    Route::post('clients/{client}/impersonate', [Admin\ImpersonationController::class, 'start'])->name('clients.impersonate');

    // Reservations
    Route::resource('reservations', Admin\ReservationController::class);
    Route::post('reservations/{reservation}/annuler', [Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('reservations/{reservation}/envoyer-contrat', [Admin\ReservationController::class, 'sendContract'])->name('reservations.send-contract');

    // Inspections
    Route::get('etats-des-lieux', [Admin\InspectionController::class, 'index'])->name('inspections.index');
    Route::get('reservations/{reservation}/etat-des-lieux/{type}', [Admin\InspectionController::class, 'create'])->name('inspections.create');
    Route::post('reservations/{reservation}/etat-des-lieux/{type}', [Admin\InspectionController::class, 'store'])->name('inspections.store');
    Route::get('etat-des-lieux/{inspection}', [Admin\InspectionController::class, 'show'])->name('inspections.show');
    Route::patch('etat-des-lieux/{inspection}/message', [Admin\InspectionController::class, 'updateMessage'])->name('inspections.update-message');

    // Security deposits
    Route::get('cautions', [Admin\SecurityDepositController::class, 'index'])->name('security-deposits.index');
    Route::patch('cautions/{deposit}/statut', [Admin\SecurityDepositController::class, 'updateStatus'])->name('security-deposits.update-status');

    // Invoices
    Route::resource('factures', Admin\InvoiceController::class)->parameters(['factures' => 'invoice']);
    Route::get('factures/{invoice}/telecharger', [Admin\InvoiceController::class, 'download'])->name('invoices.download');

    // Blocked dates
    Route::get('blocages', [Admin\BlockedDateController::class, 'index'])->name('blocked-dates.index');
    Route::post('blocages', [Admin\BlockedDateController::class, 'store'])->name('blocked-dates.store');
    Route::delete('blocages/{blockedDate}', [Admin\BlockedDateController::class, 'destroy'])->name('blocked-dates.destroy');
    Route::get('blocages/json', [Admin\BlockedDateController::class, 'getJson'])->name('blocked-dates.json');

    // Mail log
    Route::get('mail-log', [Admin\MailLogController::class, 'index'])->name('mail-log.index');
    Route::get('mail-log/{mailLog}', [Admin\MailLogController::class, 'show'])->name('mail-log.show');
    Route::get('mail-log/{mailLog}/apercu', [Admin\MailLogController::class, 'preview'])->name('mail-log.preview');
    Route::delete('mail-log/{mailLog}', [Admin\MailLogController::class, 'destroy'])->name('mail-log.destroy');

    // Promo codes
    Route::get('codes-promo', [Admin\PromoCodeController::class, 'index'])->name('promo-codes.index');
    Route::post('codes-promo', [Admin\PromoCodeController::class, 'store'])->name('promo-codes.store');
    Route::post('codes-promo/{promoCode}/toggle', [Admin\PromoCodeController::class, 'toggle'])->name('promo-codes.toggle');
    Route::delete('codes-promo/{promoCode}', [Admin\PromoCodeController::class, 'destroy'])->name('promo-codes.destroy');

    // Team
    Route::get('equipe', [Admin\TeamController::class, 'index'])->name('team.index');
    Route::post('equipe', [Admin\TeamController::class, 'store'])->name('team.store');
    Route::put('equipe/{user}', [Admin\TeamController::class, 'update'])->name('team.update');
    Route::delete('equipe/{user}', [Admin\TeamController::class, 'destroy'])->name('team.destroy');

    // Settings
    Route::get('parametres', [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('parametres', [Admin\SettingsController::class, 'update'])->name('settings.update');

    // Contract PDF
    Route::get('reservations/{reservation}/contrat', function (\App\Models\Reservation $reservation) {
        return app(\App\Services\PdfService::class)->generateContract($reservation)
            ->download('contrat-' . $reservation->reference . '.pdf');
    })->name('reservations.contract');

    // Inspection PDF
    Route::get('etat-des-lieux/{inspection}/pdf', function (\App\Models\Inspection $inspection) {
        return app(\App\Services\PdfService::class)->generateInspectionReport($inspection)
            ->download('etat-lieux-' . $inspection->id . '.pdf');
    })->name('inspections.pdf');
});

// Client area
Route::prefix('espace-client')->name('client.')->middleware(['auth', 'client'])->group(function () {
    Route::get('/', [Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [Client\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/mot-de-passe', [Client\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profil/supprimer', [Client\ProfileController::class, 'requestDeletion'])->name('profile.delete-request');

    Route::get('/reservations', [Client\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/nouvelle-reservation', [Client\ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/nouvelle-reservation', [Client\ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [Client\ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/paiement-confirme', [Client\ReservationController::class, 'paymentSuccess'])->name('reservations.payment-success');
    Route::post('/reservations/{reservation}/annuler', [Client\ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::post('/disponibilites', [Client\ReservationController::class, 'getAvailability'])->name('availability');
    Route::post('/verifier-code-promo', [Client\ReservationController::class, 'checkPromoCode'])->name('promo.check');

    Route::get('/factures', [Client\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/factures/{invoice}/telecharger', [Client\InvoiceController::class, 'download'])->name('invoices.download');

    Route::get('/etats-des-lieux/{inspection}/pdf', function (\App\Models\Inspection $inspection) {
        abort_unless($inspection->reservation->client_id === auth()->id(), 403);
        return app(\App\Services\PdfService::class)->generateInspectionReport($inspection)
            ->download('etat-lieux-' . $inspection->type . '.pdf');
    })->name('inspections.pdf');
});

// Stop impersonation (accessible from client area)
Route::post('/impersonate/stop', [Admin\ImpersonationController::class, 'stop'])->middleware('auth')->name('impersonate.stop');

// Stripe webhook (no CSRF)
Route::post('/webhook/stripe', [App\Http\Controllers\StripeWebhookController::class, 'handle'])->name('stripe.webhook');
