<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\Reservation;
use App\Models\DamageCharge;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionService
{
    public function createInspection(Reservation $reservation, string $type, array $data): Inspection
    {
        // Save signatures
        $clientSigPath = $this->saveSignature($data['client_signature'], "signatures/{$reservation->id}_{$type}_client");
        $adminSigPath = $this->saveSignature($data['admin_signature'], "signatures/{$reservation->id}_{$type}_admin");

        $inspection = $reservation->inspections()->create([
            'type' => $type,
            'admin_id' => auth()->id(),
            'general_notes' => $data['general_notes'] ?? null,
            'client_signature_path' => $clientSigPath,
            'admin_signature_path' => $adminSigPath,
            'signed_at' => now(),
        ]);

        foreach ($data['items'] as $itemData) {
            $inspection->items()->create([
                'checklist_item_id' => $itemData['checklist_item_id'],
                'condition' => $itemData['condition'],
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        // Handle photos
        if (!empty($data['photos']) && is_array($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                if ($photo instanceof \Illuminate\Http\UploadedFile) {
                    $path = $photo->store('inspections', 'public');
                    $inspection->photos()->create(['path' => $path]);
                }
            }
        }

        return $inspection;
    }

    public function processDamageCharges(Reservation $reservation, Inspection $returnInspection): void
    {
        $departureInspection = $reservation->departureInspection;
        if (!$departureInspection) return;

        $totalDamage = 0;

        foreach ($returnInspection->items as $returnItem) {
            $departureItem = $departureInspection->items
                ->where('checklist_item_id', $returnItem->checklist_item_id)
                ->first();

            if (!$departureItem) continue;

            $worsened = $this->conditionWorsened($departureItem->condition, $returnItem->condition);
            if ($worsened) {
                $charge = DamageCharge::create([
                    'reservation_id' => $reservation->id,
                    'inspection_item_id' => $returnItem->id,
                    'label' => "Dégradation: " . $returnItem->checklistItem->label,
                    'description' => "État départ: {$departureItem->condition_label} → État retour: {$returnItem->condition_label}",
                    'amount' => 0,
                ]);
                $totalDamage += 0;
            }
        }

        if ($totalDamage > 0) {
            Invoice::create([
                'client_id' => $reservation->client_id,
                'reservation_id' => $reservation->id,
                'type' => 'damage',
                'amount' => $totalDamage,
                'status' => 'pending',
            ]);
        }
    }

    private function conditionWorsened(string $before, string $after): bool
    {
        $scale = ['good' => 3, 'worn' => 2, 'damaged' => 1, 'missing' => 0];
        return ($scale[$after] ?? 3) < ($scale[$before] ?? 3);
    }

    private function saveSignature(string $base64Data, string $name): string
    {
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $decoded = base64_decode($data);
        $path = "signatures/{$name}.png";
        Storage::disk('public')->put($path, $decoded);
        return $path;
    }
}
