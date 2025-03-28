<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\Education;
use App\Models\RfidCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $admins = User::with(['division', 'position', 'education', 'rfidCards'])
            ->where('role', 'admin')
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhereHas('division', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('position', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->get();

        $divisions = Division::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $educations = Education::orderBy('name')->get();
        $rfidCards = RfidCard::whereDoesntHave('user')->orWhereNull('user_id')->get();

        return view('admin.admins.index', compact(
            'admins',
            'divisions',
            'positions',
            'educations',
            'rfidCards',
            'search'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => 'nullable|string|max:20|unique:users',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'education_id' => 'nullable|exists:education,id',
            'rfid_card_id' => 'nullable|exists:rfid_cards,id',
            'phone_number' => 'nullable|string|max:15',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'join_date' => 'nullable|date',
            'status' => 'nullable|in:Active,Inactive,Resigned',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nip' => $request->nip,
            'role' => 'admin',
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'education_id' => $request->education_id,
            'rfid_card_id' => $request->rfid_card_id,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'city' => $request->city,
            'address' => $request->address,
            'join_date' => $request->join_date,
            'status' => $request->status,
        ]);

        if ($request->hasFile('profile_photo')) {
            $user->updateProfilePhoto($request->file('profile_photo'));
        }

        if ($request->rfid_card_id) {
            RfidCard::where('id', $request->rfid_card_id)->update(['user_id' => $user->id]);
        }

        return redirect()->route('admin.admins.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Admin created successfully'
            ]);
    }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$admin->id,
            'nip' => 'nullable|string|max:20|unique:users,nip,'.$admin->id,
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'education_id' => 'nullable|exists:education,id',
            'rfid_card_id' => 'nullable|exists:rfid_cards,id',
            'phone_number' => 'nullable|string|max:15',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'join_date' => 'nullable|date',
            'status' => 'nullable|in:Active,Inactive,Resigned',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $previousRfidCardId = $admin->rfid_card_id;

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'education_id' => $request->education_id,
            'rfid_card_id' => $request->rfid_card_id,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'city' => $request->city,
            'address' => $request->address,
            'join_date' => $request->join_date,
            'status' => $request->status,
        ]);

        if ($request->hasFile('profile_photo')) {
            $admin->updateProfilePhoto($request->file('profile_photo'));
        }

        if ($previousRfidCardId && $previousRfidCardId != $request->rfid_card_id) {
            RfidCard::where('id', $previousRfidCardId)->update(['user_id' => null]);
        }
        if ($request->rfid_card_id) {
            RfidCard::where('id', $request->rfid_card_id)->update(['user_id' => $admin->id]);
        }

        return redirect()->route('admin.admins.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Admin updated successfully'
            ]);
    }

    public function destroy(User $admin)
    {
        if ($admin->rfid_card_id) {
            RfidCard::where('id', $admin->rfid_card_id)->update(['user_id' => null]);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Admin deleted successfully'
            ]);
    }
}
