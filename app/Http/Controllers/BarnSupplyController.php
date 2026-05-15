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
                // DEBUG: Dump the upload result or error
                $uploadResult = $this->uploadToCloudinaryWithRetry($request->file('supply_image'));
                dd($uploadResult); // <-- This will show you the result on a blank page
                $imgUrl = $uploadResult;
            } catch (\Exception $e) {
                // Also log the error to laravel.log
                Log::error('Cloudinary store error: ' . $e->getMessage());
                dd('Upload failed: ' . $e->getMessage()); // <-- This will show the error
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
                // DEBUG: Dump the upload result or error
                $uploadResult = $this->uploadToCloudinaryWithRetry($request->file('supply_image'));
                dd($uploadResult); // <-- This will show you the result on a blank page
                $imgUrl = $uploadResult;
            } catch (\Exception $e) {
                Log::error('Cloudinary update error: ' . $e->getMessage());
                dd('Upload failed: ' . $e->getMessage()); // <-- This will show the error
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

    // Helper method: upload to Cloudinary with retry and fallback
    private function uploadToCloudinaryWithRetry($file, $maxRetries = 3, $delaySeconds = 2)
{
    $attempt = 0;
    $lastException = null;

    while ($attempt < $maxRetries) {
        try {
            // 1. Get the file content or path
            $fileToUpload = $file->getRealPath();
            if (!$fileToUpload || !file_exists($fileToUpload)) {
                $fileToUpload = $file->get(); // Fallback to raw content
            }

            // 2. Fetch Cloudinary credentials from environment
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey    = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');

            if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
                throw new \Exception('Missing Cloudinary credentials. Check your Render environment variables.');
            }

            // 3. Prepare the API request
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
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Add a timeout to prevent hanging

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
                throw new \Exception('Cloudinary response missing secure_url. Full response: ' . print_r($data, true));
            }

            // Log success for monitoring
            Log::info('Cloudinary upload successful', ['url' => $data['secure_url']]);

            return $data['secure_url'];

        } catch (\Exception $e) {
            $lastException = $e;
            $attempt++;
            Log::warning("Cloudinary upload attempt {$attempt} failed", ['error' => $e->getMessage()]);
            if ($attempt < $maxRetries) {
                sleep($delaySeconds);
            }
        }
    }

    throw new \Exception('Cloudinary upload failed after ' . $maxRetries . ' attempts: ' . $lastException->getMessage());
}

    // Redirect unused methods
    public function show(BarnSupply $inventory) { return redirect()->route('inventory.index'); }
    public function create()                    { return redirect()->route('inventory.index'); }
    public function edit(BarnSupply $inventory) { return redirect()->route('inventory.index'); }
}