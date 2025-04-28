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
    public function index()
    {
        //
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
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
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
             // accepts only images up to 2MB
        ]);
    
        // Handle file upload
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . $file->getClientOriginalName(); // e.g. 1714355344_profilepic.jpg
            $filepath = $file->storeAs('uploads', $filename, 'public'); // store to storage/app/public/uploads
        }
    
        // Create the record
        $person = new Employee();
        $person->name = $request->name;
        $person->id_number = $request->id_number;
        $person->college = $request->college;
        $person->classification = $request->classification;
        $person->picture = $filepath; // save only the relative path
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
            'id_number' => 'required|string|max:255|unique:employees,id_number,' . $id, // Allow the current id to remain unique
            'college' => 'required|string',
            'classification' => 'required|string',   
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Make picture optional
        ]);
    
        $employee = Employee::findOrFail($id);
        
        // Store the old image path for deletion
        $oldImagePath = $employee->picture;
    
        // Handle new image upload if a new file is provided
        if ($request->hasFile('picture')) {
            // Delete the old image if it exists
            if ($oldImagePath && Storage::exists('public/' . $oldImagePath)) {
                Storage::delete('public/' . $oldImagePath);
            }
    
            // Handle the new file upload
            $file = $request->file('picture');
            $filename = time() . '_' . $file->getClientOriginalName(); 
            $filepath = $file->storeAs('uploads', $filename, 'public');
        } else {
            // If no new image is uploaded, keep the old image path
            $filepath = $oldImagePath;
        }
    
        // Update the employee data
        $employee->update([
            'name' => $request->name,
            'id_number' => $request->id_number,
            'college' => $request->college,
            'classification' => $request->classification,
            'picture' => $filepath, // Save the updated image path
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

    // Generate the QR code
   
    $qrCode = QrCode::size(200)->generate(json_encode([
        'id' => $employee->id,
        'name' => $employee->name,
        'id_number' => $employee->id_number,
        'college' => $employee->college,
        'classification' => $employee->classification,
        'picture' => $employee->picture,
    ]));
    // Define the file name for the QR code image
    $fileName = 'qr_code_' . $employee->id_number . '.png';

    // Store the QR code as an image and return the response to download it
    return response($qrCode)
        ->header('Content-Type', 'image/png')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}
}
