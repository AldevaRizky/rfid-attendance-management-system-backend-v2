<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index()
    {
        $educations = Education::orderByRaw('CAST(level AS UNSIGNED)')->orderBy('name')->get();
        return view('admin.educations.index', compact('educations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:education',
            'level' => 'required|integer|min:1'
        ]);

        Education::create($validated);

        return redirect()->route('admin.educations.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Education created successfully'
            ]);
    }

    public function update(Request $request, Education $education)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:education,name,'.$education->id,
            'level' => 'required|integer|min:1'
        ]);

        $education->update($validated);

        return redirect()->route('admin.educations.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Education updated successfully'
            ]);
    }

    public function destroy(Education $education)
    {
        $education->delete();

        return redirect()->route('admin.educations.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Education deleted successfully'
            ]);
    }
}
