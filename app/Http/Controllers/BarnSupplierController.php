<?php

namespace App\Http\Controllers;

use App\Models\Barn;
use App\Models\BarnSupplier;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarnSupplierController extends Controller
{
    private function getOwnerBarn()
    {
        return Barn::where('barn_owner_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $barn = $this->getOwnerBarn();

        $suppliers = BarnSupplier::with('category')
            ->where('barn_id', $barn->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $categories = Category::orderBy('category_name')->get();

        $suppliersData = $suppliers->map(function ($s) {
            return [
                'id'              => $s->id,
                'display_id'      => 'SUP' . str_pad($s->id, 3, '0', STR_PAD_LEFT),
                'supplier_name'   => $s->supplier_name,
                'category_id'     => $s->category_id,
                'category_name'   => $s->category->category_name ?? '—',
                'contact_number'  => $s->contact_number ?? '',
                'supplier_status' => $s->supplier_status,
                'update_url'      => route('suppliers.update', $s->id),
                'delete_url'      => route('suppliers.destroy', $s->id),
            ];
        })->values();

        return view('barn_owner_suppliers.index', compact('barn', 'suppliers', 'categories', 'suppliersData'));
    }

    public function store(Request $request)
    {
        $barn = $this->getOwnerBarn();

        $request->validate([
            'supplier_name'  => 'required|string|max:200',
            'category_id'    => 'required|exists:categories,id',
            'contact_number' => 'nullable|string|max:30',
        ]);

        BarnSupplier::create([
            'barn_id'         => $barn->id,
            'category_id'     => $request->category_id,
            'supplier_name'   => $request->supplier_name,
            'contact_number'  => $request->contact_number,
            'supplier_status' => 'active',
        ]);

        return redirect()->route('suppliers.index')
                         ->with('success', "Supplier \"{$request->supplier_name}\" added successfully.");
    }

    public function update(Request $request, BarnSupplier $supplier)
    {
        $barn = $this->getOwnerBarn();
        abort_if($supplier->barn_id !== $barn->id, 403);

        $request->validate([
            'supplier_name'   => 'required|string|max:200',
            'category_id'     => 'required|exists:categories,id',
            'contact_number'  => 'nullable|string|max:30',
            'supplier_status' => 'required|in:active,inactive',
        ]);

        $supplier->update([
            'supplier_name'   => $request->supplier_name,
            'category_id'     => $request->category_id,
            'contact_number'  => $request->contact_number,
            'supplier_status' => $request->supplier_status,
        ]);

        return redirect()->route('suppliers.index')
                         ->with('success', "Supplier \"{$supplier->supplier_name}\" updated successfully.");
    }

    public function destroy(BarnSupplier $supplier)
    {
        $barn = $this->getOwnerBarn();
        abort_if($supplier->barn_id !== $barn->id, 403);

        $name = $supplier->supplier_name;
        $newStatus = $supplier->supplier_status === 'active' ? 'inactive' : 'active';

        $supplier->update(['supplier_status' => $newStatus]);

        $action = $newStatus === 'active' ? 'reactivated' : 'deactivated';

        return redirect()->route('suppliers.index')
                         ->with('success', "Supplier \"{$name}\" has been {$action} successfully.");
    }

    public function show(BarnSupplier $supplier) { return redirect()->route('suppliers.index'); }
    public function create()                     { return redirect()->route('suppliers.index'); }
    public function edit(BarnSupplier $supplier) { return redirect()->route('suppliers.index'); }
}