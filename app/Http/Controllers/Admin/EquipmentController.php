<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentPhoto;
use App\Models\ChecklistItem;
use App\Models\DamageScaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::withCount('reservationItems')->latest()->paginate(20);
        return view('admin.equipment.index', compact('equipment'));
    }

    public function create()
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:100|unique:equipment,reference',
            'description' => 'nullable|string',
            'daily_rate' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'file|mimes:jpg,jpeg,png,gif,webp,bmp|max:5120',
        ]);

        $equipment = Equipment::create([
            'name' => $data['name'],
            'reference' => $data['reference'],
            'description' => $data['description'] ?? null,
            'daily_rate' => $data['daily_rate'],
            'deposit_amount' => $data['deposit_amount'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('equipment', 'public');
                $equipment->photos()->create([
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.materiel.show', $equipment)
            ->with('success', 'Matériel créé avec succès.');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['photos', 'checklistItems', 'damageScaleItems']);
        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $equipment->load(['photos', 'checklistItems', 'damageScaleItems']);
        return view('admin.equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:100|unique:equipment,reference,' . $equipment->id,
            'description' => 'nullable|string',
            'daily_rate' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'file|mimes:jpg,jpeg,png,gif,webp,bmp|max:5120',
        ]);

        $equipment->update([
            'name' => $data['name'],
            'reference' => $data['reference'],
            'description' => $data['description'] ?? null,
            'daily_rate' => $data['daily_rate'],
            'deposit_amount' => $data['deposit_amount'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('equipment', 'public');
                $equipment->photos()->create([
                    'path' => $path,
                    'is_primary' => $equipment->photos()->count() === 0 && $index === 0,
                    'order' => $equipment->photos()->count() + $index,
                ]);
            }
        }

        return redirect()->route('admin.materiel.show', $equipment)
            ->with('success', 'Matériel mis à jour.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('admin.materiel.index')
            ->with('success', 'Matériel supprimé.');
    }

    public function deletePhoto(EquipmentPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return back()->with('success', 'Photo supprimée.');
    }

    public function setPrimaryPhoto(EquipmentPhoto $photo)
    {
        $photo->equipment->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);
        return back()->with('success', 'Photo principale mise à jour.');
    }

    public function storeChecklistItem(Request $request, Equipment $equipment)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $equipment->checklistItems()->create([
            'label' => $request->label,
            'description' => $request->description,
            'order' => $equipment->checklistItems()->count(),
        ]);

        return back()->with('success', 'Élément de checklist ajouté.');
    }

    public function destroyChecklistItem(ChecklistItem $item)
    {
        $item->delete();
        return back()->with('success', 'Élément supprimé.');
    }

    public function storeDamageScaleItem(Request $request, Equipment $equipment)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $equipment->damageScaleItems()->create($request->only('label', 'description', 'amount'));
        return back()->with('success', 'Barème ajouté.');
    }

    public function destroyDamageScaleItem(DamageScaleItem $item)
    {
        $item->delete();
        return back()->with('success', 'Barème supprimé.');
    }
}
