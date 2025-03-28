<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('level')->orderBy('name')->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:positions',
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1'
        ]);

        Position::create($validated);

        return redirect()->route('admin.positions.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Position created successfully'
            ]);
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:positions,name,'.$position->id,
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1'
        ]);

        $position->update($validated);

        return redirect()->route('admin.positions.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Position updated successfully'
            ]);
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Position deleted successfully'
            ]);
    }
}
