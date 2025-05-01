<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Record</title>
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination-btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #007bff;
            text-decoration: none;
        }
        .pagination-btn.active {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }
        .pagination-btn.disabled {
            color: #6c757d;
            pointer-events: none;
            background-color: #e9ecef;
        }
    </style>
</head>
<body>

<h1 class="mb-4">Employees Record</h1>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('employees.create') }}">Add Employee Data</a>

<form method="GET" action="{{ route('employees.index') }}">
    <label for="classification">Filter by Classification:</label>
    <select name="classification" id="classification">
        <option value="">All</option>
        @foreach ($classifications as $item)
            <option value="{{ $item }}" {{ request('classification') == $item ? 'selected' : '' }}>
                {{ $item }}
            </option>
        @endforeach
    </select>

    <label for="college">Filter by College:</label>
    <select name="college" id="college">
        <option value="">All</option>
        @foreach ($colleges as $item)
            <option value="{{ $item }}" {{ request('college') == $item ? 'selected' : '' }}>
                {{ $item }}
            </option>
        @endforeach
    </select>

    <button type="submit">Apply Filters</button>
</form>
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
                    <a href="{{ route('employees.edit', $employee->id) }}">Edit</a>
                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('employees.show', $employee->id) }}">Show</a>
                </td>
                <td>
                    <a href="{{ route('employees.downloadQrCode', $employee->id) }}" class="btn btn-primary">Download QR Code</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Custom Pagination -->
<div class="pagination">
    @if ($employees->onFirstPage())
        <span class="pagination-btn disabled">First</span>
        <span class="pagination-btn disabled">Previous</span>
    @else
        <a href="{{ $employees->url(1) }}" class="pagination-btn">First</a>
        <a href="{{ $employees->previousPageUrl() }}" class="pagination-btn">Previous</a>
    @endif

    @foreach ($employees->getUrlRange(max(1, $employees->currentPage() - 2), min($employees->lastPage(), $employees->currentPage() + 2)) as $page => $url)
        <a href="{{ $url }}" class="pagination-btn {{ $employees->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
    @endforeach

    @if ($employees->hasMorePages())
        <a href="{{ $employees->nextPageUrl() }}" class="pagination-btn">Next</a>
        <a href="{{ $employees->url($employees->lastPage()) }}" class="pagination-btn">Last</a>
    @else
        <span class="pagination-btn disabled">Next</span>
        <span class="pagination-btn disabled">Last</span>
    @endif
</div>

</body>
</html>
