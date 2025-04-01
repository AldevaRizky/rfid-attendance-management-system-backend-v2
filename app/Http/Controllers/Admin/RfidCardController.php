<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class RfidCardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $rfidCards = RfidCard::with('user')
            ->when($search, function ($query) use ($search) {
                return $query->where('card_number', 'like', "%{$search}%")
                             ->orWhereHas('user', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->get();

        $users = User::whereDoesntHave('rfidCards')->orderBy('name')->get();

        return view('admin.rfid-cards.index', compact('rfidCards', 'users', 'search'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'card_number' => 'required|string|max:50|unique:rfid_cards',
                'user_id' => 'required|exists:users,id',
                'status' => 'required|in:Active,Inactive,Blocked',
                'issued_date' => 'required|date',
                'expired_date' => 'required|date|after:issued_date'
            ]);

            RfidCard::create($validated);

            return redirect()->route('admin.rfid-cards.index')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'RFID Card created successfully'
                ]);

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Validation error: Please check your input'
                ]);

        } catch (QueryException $e) {
            return redirect()->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Database error: Failed to create RFID Card'
                ]);
        }
    }

    public function update(Request $request, RfidCard $rfidCard)
    {
        try {
            $validated = $request->validate([
                'card_number' => 'required|string|max:50|unique:rfid_cards,card_number,'.$rfidCard->id,
                'user_id' => 'required|exists:users,id',
                'status' => 'required|in:Active,Inactive,Blocked',
                'issued_date' => 'required|date',
                'expired_date' => 'required|date|after:issued_date'
            ]);

            $rfidCard->update($validated);

            return redirect()->route('admin.rfid-cards.index')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'RFID Card updated successfully'
                ]);

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Validation error: Please check your input'
                ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Error updating RFID Card: ' . $e->getMessage()
                ]);
        }
    }

    public function destroy(RfidCard $rfidCard)
    {
        try {
            $rfidCard->delete();

            return redirect()->route('admin.rfid-cards.index')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'RFID Card deleted successfully'
                ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Error deleting RFID Card: ' . $e->getMessage()
                ]);
        }
    }
}
