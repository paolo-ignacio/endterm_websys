<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('classification')) {
            $query->where('classification', $request->classification);
        }

        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        $employees = $query->paginate(10)->appends($request->query());

        // Get unique classifications and colleges for filter dropdowns
        $classifications = Employee::select('classification')->distinct()->pluck('classification');
        $colleges = Employee::select('college')->distinct()->pluck('college');

        return view('employees.index', compact('employees', 'classifications', 'colleges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:employees,id_number',
            'college' => 'required|string',
            'classification' => 'required|string',   
           
            'picture' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
             // accepts only images up to 2MB
        ]);
    
        // Handle file upload
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . $file->getClientOriginalName(); // e.g. 1714355344_profilepic.jpg
            $file->move(public_path('images'), $filename); // store to storage/app/public/uploads
        }
    
        // Create the record
        $person = new Employee();
        $person->name = $request->name;
        $person->id_number = $request->id_number;
        $person->college = $request->college;
        $person->classification = $request->classification;
        $person->picture = $file; // save only the relative path
        $person->save();
 
  

        return redirect()->route('employees.index')->with('success', 'Person added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $qr = QrCode::size(200)->generate(json_encode([
            'id' => $employee->id,
            'name' => $employee->name,
            'id_number' => $employee->id_number,
            'college' => $employee->college,
            'classification' => $employee->classification,
            'picture' => $employee->picture,
        ]));

        return view('employees.show', compact('employee', 'qr'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:employees,id_number,' . $id,
            'college' => 'required|string',
            'classification' => 'required|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
        ]);
    
        $employee = Employee::findOrFail($id);
    
        $oldImagePath = $employee->picture;
    
        if ($request->hasFile('picture')) {
            // Delete old image if it exists
            if ($oldImagePath && file_exists(public_path('images/' . $oldImagePath))) {
                unlink(public_path('images/' . $oldImagePath));
            }
        
      
    
            $file = $request->file('picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
    
            // ✅ Save new path
            $filepath = $filename;
        } else {
            // ✅ Keep old path
            $filepath = $oldImagePath;
        }
    
        $employee->update([
            'name' => $request->name,
            'id_number' => $request->id_number,
            'college' => $request->college,
            'classification' => $request->classification,
            'picture' => $filepath, // ✅ Correctly stores either new or old
        ]);
    
        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee data updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->picture && Storage::exists('public/' . $employee->picture)) {
            // Delete the image from storage
            Storage::delete('public/' . $employee->picture);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee data deleted!');
    }

   
public function downloadQrCode($id)
{
    $employee = Employee::findOrFail($id);

    $qrCode = QrCode::format('png') // Make sure it's a PNG
        ->size(200)
        ->generate(json_encode([
            'name' => $employee->name,
            'id_number' => $employee->id_number,
            'college' => $employee->college,
            'classification' => $employee->classification,
            'picture' => $employee->picture,
        ]));

    $fileName = 'qr_code_' . $employee->id_number . '.png';

    return response($qrCode)
        ->header('Content-Type', 'image/png')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}
}
