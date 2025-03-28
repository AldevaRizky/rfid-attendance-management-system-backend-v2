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

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $employees = User::with(['division', 'position', 'education', 'rfidCards'])
            ->where('role', 'employee')
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhereHas('division', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('position', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('education', function ($q) use ($search) {
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

        return view('admin.employees.index', compact(
            'employees',
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
            'nip' => 'required|string|max:20|unique:users',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'education_id' => 'required|exists:education,id',
            'rfid_card_id' => 'nullable|exists:rfid_cards,id',
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'join_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Resigned',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nip' => $request->nip,
            'role' => 'employee',
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

        // Update RFID card if assigned
        if ($request->rfid_card_id) {
            RfidCard::where('id', $request->rfid_card_id)->update(['user_id' => $user->id]);
        }

        return redirect()->route('admin.employees.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Employee created successfully'
            ]);
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$employee->id,
            'nip' => 'required|string|max:20|unique:users,nip,'.$employee->id,
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'education_id' => 'required|exists:education,id',
            'rfid_card_id' => 'nullable|exists:rfid_cards,id',
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'join_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Resigned',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Get previous RFID card
        $previousRfidCardId = $employee->rfid_card_id;

        $employee->update([
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
            $employee->updateProfilePhoto($request->file('profile_photo'));
        }

        // Update RFID card assignments
        if ($previousRfidCardId && $previousRfidCardId != $request->rfid_card_id) {
            RfidCard::where('id', $previousRfidCardId)->update(['user_id' => null]);
        }
        if ($request->rfid_card_id) {
            RfidCard::where('id', $request->rfid_card_id)->update(['user_id' => $employee->id]);
        }

        return redirect()->route('admin.employees.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Employee updated successfully'
            ]);
    }

    public function destroy(User $employee)
    {
        // Release RFID card before deletion
        if ($employee->rfid_card_id) {
            RfidCard::where('id', $employee->rfid_card_id)->update(['user_id' => null]);
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Employee deleted successfully'
            ]);
    }
}
