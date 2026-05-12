<?php

namespace App\Http\Controllers;

use App\Models\Barn;
use App\Models\BarnSupply;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BarnSupplyController extends Controller
{
    private function getOwnerBarn()
    {
        return Barn::where('barn_owner_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $barn = $this->getOwnerBarn();

        $supplies = BarnSupply::with(['category'])
            ->where('barn_id', $barn->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::orderBy('category_name')->get();

        $suppliesData = $supplies->map(function ($s) {
            $catName = $s->category->category_name ?? 'N/A';
            $supId   = strtoupper(substr($catName, 0, 3)) . str_pad($s->id, 4, '0', STR_PAD_LEFT);
            
            $isLow   = $s->stock <= $s->reorder_level;
            $isOut   = $s->stock == 0;
            $statusLabel = $isOut ? 'Out of Stock' : ($isLow ? 'Low Stock' : 'In Stock');

            return [
                'id'              => $s->id,
                'display_id'      => $supId,
                'supply_name'     => $s->supply_name,
                'category_id'     => $s->category_id,
                'category_name'   => $catName,
                'stock'           => $s->stock,
                'reorder_level'   => $s->reorder_level,
                'status_label'    => $statusLabel,
                'supply_status'   => $s->supply_status,
                'img_url'         => $s->supply_img_path, 
                'edit_url'        => route('inventory.update', $s->id),
                'delete_url'      => route('inventory.destroy', $s->id),
            ];
        })->values();

        return view('barn_owner_inventory.index', compact('barn', 'supplies', 'categories', 'suppliesData'));
    }

  public function store(Request $request)
{
    $barn = $this->getOwnerBarn();

    $request->validate([
        'supply_name'   => 'required|string|max:200',
        'category_id'   => 'required|exists:categories,id',
        'reorder_level' => 'required|integer|min:0',
        'supply_image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $imgUrl = null;

    if ($request->hasFile('supply_image')) {
        try {
            // Check if Cloudinary is properly configured
            if (!config('cloudinary.cloud_name')) {
                throw new \Exception('Cloudinary is not configured properly.');
            }

            $file = $request->file('supply_image');

            $uploadResult = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'barn_supplies',
                'use_filename' => true,
                'unique_filename' => true,
            ]);

            $imgUrl = $uploadResult->getSecurePath();

        } catch (\Exception $e) {
            \Log::error('Cloudinary Upload Failed: ' . $e->getMessage());
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Image upload failed: ' . $e->getMessage());
        }
    }

    // Create supply (this part works as you confirmed)
    BarnSupply::create([
        'barn_id'         => $barn->id,
        'category_id'     => $request->category_id,
        'supply_name'     => $request->supply_name,
        'supply_img_path' => $imgUrl,
        'stock'           => 0,
        'reorder_level'   => $request->reorder_level,
        'supply_status'   => 'active',
    ]);

    return redirect()->route('inventory.index')
                     ->with('success', "Supply \"{$request->supply_name}\" added successfully.");
}

    public function store(Request $request)
{
    $barn = $this->getOwnerBarn();

    $request->validate([
        'supply_name'   => 'required|string|max:200',
        'category_id'   => 'required|exists:categories,id',
        'reorder_level' => 'required|integer|min:0',
        'supply_image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $imgUrl = null;

    if ($request->hasFile('supply_image')) {
        try {
            // Check if Cloudinary is properly configured
            if (!config('cloudinary.cloud_name')) {
                throw new \Exception('Cloudinary is not configured properly.');
            }

            $file = $request->file('supply_image');

            $uploadResult = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'barn_supplies',
                'use_filename' => true,
                'unique_filename' => true,
            ]);

            $imgUrl = $uploadResult->getSecurePath();

        } catch (\Exception $e) {
            \Log::error('Cloudinary Upload Failed: ' . $e->getMessage());
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Image upload failed: ' . $e->getMessage());
        }
    }

    // Create supply (this part works as you confirmed)
    BarnSupply::create([
        'barn_id'         => $barn->id,
        'category_id'     => $request->category_id,
        'supply_name'     => $request->supply_name,
        'supply_img_path' => $imgUrl,
        'stock'           => 0,
        'reorder_level'   => $request->reorder_level,
        'supply_status'   => 'active',
    ]);

    return redirect()->route('inventory.index')
                     ->with('success', "Supply \"{$request->supply_name}\" added successfully.");
}
    public function destroy(BarnSupply $inventory)
    {
        $barn = $this->getOwnerBarn();
        abort_if($inventory->barn_id !== $barn->id, 403);

        $name = $inventory->supply_name;
        $newStatus = $inventory->supply_status === 'active' ? 'inactive' : 'active';

        $inventory->update(['supply_status' => $newStatus]);

        $action = $newStatus === 'active' ? 'reactivated' : 'deactivated';

        return redirect()->route('inventory.index')
                         ->with('success', "Supply \"{$name}\" has been {$action} successfully.");
    }

    // Redirect unused methods
    public function show(BarnSupply $inventory) { return redirect()->route('inventory.index'); }
    public function create()                    { return redirect()->route('inventory.index'); }
    public function edit(BarnSupply $inventory) { return redirect()->route('inventory.index'); }
}