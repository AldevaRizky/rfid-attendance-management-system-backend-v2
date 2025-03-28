<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::with('head')
            ->orderBy('name')
            ->get();

        $heads = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('admin.divisions.index', compact('divisions', 'heads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:divisions',
            'description' => 'nullable|string|max:255',
            'head_user_id' => 'nullable|exists:users,id'
        ]);

        Division::create($validated);

        return redirect()->route('admin.divisions.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Division created successfully'
            ]);
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name,'.$division->id,
            'description' => 'nullable|string|max:255',
            'head_user_id' => 'nullable|exists:users,id'
        ]);

        $division->update($validated);

        return redirect()->route('admin.divisions.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Division updated successfully'
            ]);
    }

    public function destroy(Division $division)
    {
        $division->delete();

        return redirect()->route('admin.divisions.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Division deleted successfully'
            ]);
    }
}
