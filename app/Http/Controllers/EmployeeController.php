<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Workplace;

class EmployeeController extends Controller
{
    public function index()
    {
        $ninjas = Employee::with('workplace')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ninjas.index', compact('ninjas'));
    }

    public function show(Employee $employee)
    {
        $employee->load('workplace');

        return view('ninjas.show', [
            'ninja' => $employee
        ]);
    }

    public function create()
    {
        $workplaces = Workplace::all();

        return view('ninjas.create', compact('workplaces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'skill' => 'required|integer|min:1|max:100',
            'bio' => 'required|string|max:1000',
            'workplace_id' => 'required|exists:workplaces,id',
        ]);

        Employee::create($validated);

        return redirect()
            ->route('employee.index')
            ->with('success', 'Ninja created successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employee.index')
            ->with('success', 'Ninja deleted successfully.');
    }
}
