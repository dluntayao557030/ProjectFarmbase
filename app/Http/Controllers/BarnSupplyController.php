<?php

namespace App\Http\Controllers;

use App\Models\Barn;
use App\Models\BarnSupply;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ->paginate(5);

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
                $imgUrl = $this->uploadToCloudinaryWithRetry($request->file('supply_image'));
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

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

    public function update(Request $request, BarnSupply $inventory)
    {
        $barn = $this->getOwnerBarn();
        abort_if($inventory->barn_id !== $barn->id, 403);

        $request->validate([
            'supply_name'   => 'required|string|max:200',
            'category_id'   => 'required|exists:categories,id',
            'reorder_level' => 'required|integer|min:0',
            'supply_image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $imgUrl = $inventory->supply_img_path;

        if ($request->hasFile('supply_image')) {
            try {
                $imgUrl = $this->uploadToCloudinaryWithRetry($request->file('supply_image'));
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

        $inventory->update([
            'category_id'     => $request->category_id,
            'supply_name'     => $request->supply_name,
            'supply_img_path' => $imgUrl,
            'reorder_level'   => $request->reorder_level,
        ]);

        return redirect()->route('inventory.index')
            ->with('success', "Supply \"{$inventory->supply_name}\" updated successfully.");
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

    private function uploadToCloudinaryWithRetry($file, $maxRetries = 3, $delaySeconds = 2)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                $fileToUpload = $file->getRealPath();
                if (!$fileToUpload || !file_exists($fileToUpload)) {
                    $fileToUpload = $file->get();
                }

                $cloudName = env('CLOUDINARY_CLOUD_NAME');
                $apiKey    = env('CLOUDINARY_API_KEY');
                $apiSecret = env('CLOUDINARY_API_SECRET');

                if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
                    throw new \Exception('Missing Cloudinary credentials in environment.');
                }

                $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
                $timestamp = time();
                $signature = sha1("folder=barn_supplies&timestamp={$timestamp}{$apiSecret}");

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'file'       => curl_file_create($fileToUpload),
                    'api_key'    => $apiKey,
                    'timestamp'  => $timestamp,
                    'signature'  => $signature,
                    'folder'     => 'barn_supplies',
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($curlError) {
                    throw new \Exception('cURL error: ' . $curlError);
                }

                if ($httpCode !== 200) {
                    throw new \Exception("Cloudinary API returned HTTP {$httpCode}. Response: " . substr($response, 0, 500));
                }

                $data = json_decode($response, true);
                if (!isset($data['secure_url'])) {
                    throw new \Exception('Cloudinary response missing secure_url.');
                }

                return $data['secure_url'];

            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                if ($attempt < $maxRetries) {
                    sleep($delaySeconds);
                }
            }
        }

        throw new \Exception('Cloudinary upload failed after ' . $maxRetries . ' attempts: ' . $lastException->getMessage());
    }

    public function show(BarnSupply $inventory) { return redirect()->route('inventory.index'); }
    public function create()                    { return redirect()->route('inventory.index'); }
    public function edit(BarnSupply $inventory) { return redirect()->route('inventory.index'); }
}