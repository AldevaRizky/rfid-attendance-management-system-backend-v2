<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('start_time')->get();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:shifts',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'grace_period' => 'required|integer|min:0',
            'max_late_time' => 'required|integer|min:0'
        ]);

        // Format waktu lengkap
        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';

        try {
            Shift::create($validated);
            return redirect()->route('admin.shifts.index')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'Shift created successfully'
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Error creating shift: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:shifts',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'grace_period' => 'required|integer|min:0',
            'max_late_time' => 'required|integer|min:0'
        ]);

        // Format waktu lengkap
        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';

        $shift->update($validated);

        return redirect()->route('admin.shifts.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Shift updated successfully'
            ]);
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('admin.shifts.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Shift deleted successfully'
            ]);
    }
}
