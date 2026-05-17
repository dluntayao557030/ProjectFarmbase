<?php

namespace App\Http\Controllers;

use App\Models\Barn;
use App\Models\BarnStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BarnStaffController extends Controller
{
    private function getOwnerBarn()
    {
        return Barn::where('barn_owner_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $barn = $this->getOwnerBarn();

        $staffs = BarnStaff::with('user')
                    ->where('barn_id', $barn->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);

        $staffsData = $staffs->map(function ($s) {
            return [
                'id'           => $s->id,
                'display_id'   => 'STF' . str_pad($s->id, 3, '0', STR_PAD_LEFT),
                'first_name'   => $s->user->first_name ?? '',
                'last_name'    => $s->user->last_name ?? '',
                'full_name'    => ($s->user->first_name ?? '') . ' ' . ($s->user->last_name ?? ''),
                'username'     => $s->user->username ?? '',
                'email'        => $s->user->email ?? '',
                'staff_status' => $s->staff_status,
                'user_id'      => $s->user_id,
                'update_url'   => route('staffs.update', $s->id),
                'delete_url'   => route('staffs.destroy', $s->id),
            ];
        })->values();

        return view('barn_owner_staffs.index', compact('barn', 'staffs', 'staffsData'));
    }

    public function store(Request $request)
    {
        $barn = $this->getOwnerBarn();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'username'   => 'required|string|max:50|unique:users,username',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'user_type'  => 'barn_staff',
        ]);

        BarnStaff::create([
            'user_id'      => $user->id,
            'barn_id'      => $barn->id,
            'staff_status' => 'active',
        ]);

        return redirect()->route('staffs.index')
                         ->with('success', "{$user->first_name} {$user->last_name} has been added as barn staff.");
    }

    public function update(Request $request, BarnStaff $staff)
    {
        $barn = $this->getOwnerBarn();
        abort_if($staff->barn_id !== $barn->id, 403);

        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'username'     => 'required|string|max:50|unique:users,username,' . $staff->user_id,
            'staff_status' => 'required|in:active,inactive',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'username'   => $request->username,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $staff->user->update($userData);
        $staff->update(['staff_status' => $request->staff_status]);

        return redirect()->route('staffs.index')
                         ->with('success', "{$staff->user->first_name} {$staff->user->last_name}'s account has been updated.");
    }

    public function destroy(BarnStaff $staff)
    {
        $barn = $this->getOwnerBarn();
        abort_if($staff->barn_id !== $barn->id, 403);

        $name = $staff->user->first_name . ' ' . $staff->user->last_name;
        $newStatus = $staff->staff_status === 'active' ? 'inactive' : 'active';

        $staff->update(['staff_status' => $newStatus]);

        $action = $newStatus === 'active' ? 'reactivated' : 'deactivated';

        return redirect()->route('staffs.index')
                         ->with('success', "{$name} has been {$action}.");
    }

    // Redirect unused methods
    public function show(BarnStaff $staff) { return redirect()->route('staffs.index'); }
    public function create()               { return redirect()->route('staffs.index'); }
    public function edit(BarnStaff $staff) { return redirect()->route('staffs.index'); }
}