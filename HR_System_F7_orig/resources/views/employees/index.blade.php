<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background-color: #f5f5f5;
            padding: 40px 20px;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-container img {
            width: 120px;
            height: auto;
        }

        h1 {
            color: #0A28D8;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }

        .alert {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        a.button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0A28D8;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #0A28D8;
            color: #fff;
        }

        table th,
        table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .action-buttons a,
        .action-buttons form {
            display: inline-block;
            margin: 3px;
        }

        .action-buttons a,
        .btn {
            padding: 6px 12px;
            background-color: #0A28D8;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }

        .action-buttons form button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .action-buttons form button:hover {
            background-color: #c82333;
        }

        .btn-download {
            background-color: #28a745;
        }

        .btn-download:hover {
            background-color: #218838;
        }
        #success-alert {
        opacity: 1;
        transition: opacity 1s ease;
        }
        #success-alert.fade-out {
            opacity: 0;
        }
        .pagination {
            text-align: center;
            margin-top: 30px;
        }

        .pagination-btn {
            display: inline-block;
            margin: 0 5px;
            padding: 8px 16px;
            background-color: #0A28D8;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .pagination-btn:hover {
            background-color: #0731a7;
        }

        .pagination-btn.disabled {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-btn.active {
            background-color: #FFDA27;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
<div class="logo-container">
            <img src="/images/psulogo.png" alt="PSU Logo">
        </div>

    <h1>Employee List</h1>
        @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            {{ session('success') }}
        </div>
    @endif
   <a href="{{ route('employees.create') }}" class="button">Add Employee Data</a>
   <a href="{{ route('attendance.report') }}" class="button">DTR Form</a>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>ID Number</th>
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
                    <td class="action-buttons">
                        <a href="{{ route('employees.edit', $employee->id) }}">Edit</a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('employees.show', $employee->id) }}" class="btn">Show</a>
                    </td>
                    <td>
                        <a href="{{ route('employees.downloadQrCode', $employee->id) }}" class="btn btn-download">Download QR Code</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    setTimeout(function () {
        var alert = document.getElementById('success-alert');
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(function () {
                alert.style.display = 'none';
            }, 1000); // Wait for fade-out to complete (matches transition: 1s)
        }
    }, 3000); // Wait 3 seconds before starting fade
</script>


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