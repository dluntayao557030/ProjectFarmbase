<?php

namespace App\Http\Controllers;

use App\Models\Barn;
use App\Models\BarnStaff;
use App\Models\BarnSupply;
use App\Models\InventoryTransaction;
use App\Models\BarnSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TransactionController extends Controller
{
    private function getStaffBarn()
    {
        $staff = BarnStaff::where('user_id', Auth::id())
                          ->where('staff_status', 'active')
                          ->firstOrFail();

        return Barn::findOrFail($staff->barn_id);
    }

    public function index()
    {
        $barn = $this->getStaffBarn();

        // Paginated supplies for the TABLE
        $supplies = BarnSupply::with([
                'category',
                'transactions' => fn($q) => $q->with('user')->latest()->limit(1)
            ])
            ->where('barn_id', $barn->id)
            ->where('supply_status', 'active')
            ->orderBy('supply_name')
            ->paginate(5);

        // ALL supplies (unpaginated) for the modal JavaScript
        $allSupplies = BarnSupply::with('category')
            ->where('barn_id', $barn->id)
            ->where('supply_status', 'active')
            ->orderBy('supply_name')
            ->get();

        $suppliers = BarnSupplier::where('barn_id', $barn->id)
                                 ->where('supplier_status', 'active')
                                 ->orderBy('supplier_name')
                                 ->get();

        $suppliersByCategory = $suppliers->groupBy('category_id')->map(function($group) {
            return $group->map(function($s) {
                return [
                    'id'             => $s->id,
                    'supplier_name'  => $s->supplier_name,
                    'contact_number' => $s->contact_number,
                ];
            });
        });

        return view('barn_staff_transaction.index', compact('barn', 'supplies', 'suppliersByCategory', 'allSupplies'));
    }

    public function stockIn(Request $request)
    {
        $barn = $this->getStaffBarn();

        $request->validate([
            'supply_id'    => 'required|exists:barn_supplies,id',
            'supplier_id'  => 'required|exists:barn_suppliers,id',
            'quantity'     => 'required|integer|min:1',
            'unit_cost'    => 'required|numeric|min:0',
            'remarks'      => 'nullable|string|max:255',
        ]);

        $supply = BarnSupply::where('id', $request->supply_id)
                            ->where('barn_id', $barn->id)
                            ->firstOrFail();

        if ($request->supplier_id) {
            $supplier = BarnSupplier::where('id', $request->supplier_id)
                                    ->where('barn_id', $barn->id)
                                    ->where('category_id', $supply->category_id)
                                    ->first();

            if (!$supplier) {
                return redirect()->route('transactions.index')
                                ->with('error', 'Selected supplier does not match the supply category.');
            }
        }

        DB::transaction(function () use ($supply, $request, $barn) {
            $supply->increment('stock', $request->quantity);

            InventoryTransaction::create([
                'barn_id'          => $barn->id,
                'user_id'          => Auth::id(),
                'supply_id'        => $supply->id,
                'supplier_id'      => $request->supplier_id,
                'transaction_type' => 'stock_in',
                'quantity'         => $request->quantity,
                'unit_cost'        => $request->unit_cost,
                'remarks'          => $request->remarks,
                'created_at'       => now('Asia/Manila'),
                'updated_at'       => now('Asia/Manila'),
            ]);
        });

        return redirect()->route('transactions.index')
                        ->with('success', "Stock In: {$request->quantity} unit(s) of \"{$supply->supply_name}\" added successfully.");
    }

    public function stockOut(Request $request)
    {
        $barn = $this->getStaffBarn();

        $request->validate([
            'supply_id' => 'required|exists:barn_supplies,id',
            'quantity'  => 'required|integer|min:1',
            'remarks'   => 'nullable|string|max:255',
        ]);

        $supply = BarnSupply::where('id', $request->supply_id)
                            ->where('barn_id', $barn->id)
                            ->firstOrFail();

        if ($supply->stock <= 0) {
            return redirect()->route('transactions.index')
                             ->with('error', "Cannot perform Stock Out. \"{$supply->supply_name}\" is currently out of stock.");
        }

        if ($request->quantity > $supply->stock) {
            return redirect()->route('transactions.index')
                             ->with('error', "Cannot deduct {$request->quantity} unit(s). Only {$supply->stock} available in stock.");
        }

        DB::transaction(function () use ($supply, $request, $barn) {
            $supply->decrement('stock', $request->quantity);

            InventoryTransaction::create([
                'barn_id'          => $barn->id,
                'user_id'          => Auth::id(),
                'supply_id'        => $supply->id,
                'supplier_id'      => null,          // allowed after SQL fix
                'transaction_type' => 'stock_out',
                'quantity'         => $request->quantity,
                'unit_cost'        => null,          // allowed after SQL fix
                'remarks'          => $request->remarks,
                'created_at'       => now('Asia/Manila'),
                'updated_at'       => now('Asia/Manila'),
            ]);
        });

        return redirect()->route('transactions.index')
                         ->with('success', "Stock Out: {$request->quantity} unit(s) of \"{$supply->supply_name}\" deducted successfully.");
    }

    public function historyData()
    {
        $staff = BarnStaff::where('user_id', Auth::id())
                          ->where('staff_status', 'active')
                          ->firstOrFail();

        $transactions = InventoryTransaction::with(['supply', 'supplier'])
            ->where('barn_id', $staff->barn_id)
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'supply_name'      => $t->supply->supply_name ?? '—',
                    'supplier_name'    => $t->supplier ? $t->supplier->supplier_name : null,
                    'transaction_type' => $t->transaction_type,
                    'quantity'         => $t->quantity,
                    'unit_cost'        => $t->unit_cost,
                    'remarks'          => $t->remarks ?? 'No remarks',
                    'created_at'       => $t->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A'),
                ];
            });

        return response()->json($transactions);
    }
}