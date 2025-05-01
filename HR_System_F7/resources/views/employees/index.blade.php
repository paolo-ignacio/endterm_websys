<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1 class="mb-4">Employees Record</h1>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('employees.create') }}">Add Employee Data</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Id Number</th>
            <th>Class Role</th>
            <th>College</th>
            <th>Actions</th>
            <th>Show</th>
            <th>Download</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $employee)  
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->id_number }}</td>
                <td>{{ $employee->classification }}</td>
                <td>{{ $employee->college }}</td>
                
                <td>
                    
                    <a href="{{ route('employees.edit', $employee->id) }}" >Edit</a>
                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                <a href="{{ route('employees.show', $employee->id) }}" >Show</a>
               
                </td>
                <td>
                <a href="{{ route('employees.downloadQrCode', $employee->id) }}" class="btn btn-primary">Download QR Code</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>