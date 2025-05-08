<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Attendance Summary</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #0A28D8;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        form label {
            margin-right: 10px;
            font-weight: 500;
        }

        form select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-right: 15px;
            font-size: 14px;
        }

        form button {
            padding: 10px 20px;
            background-color: #0A28D8;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
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

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-btn {
            display: inline-block;
            padding: 8px 14px;
            margin: 0 5px;
            background-color: #0A28D8;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .pagination-btn.disabled {
            background-color: #ccc;
            cursor: default;
        }

        .pagination-btn.active {
            background-color: #FFDA27;

            font-weight: bold;
        }
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }
        .logo-container img {
            width: 150px;
            margin-bottom: 15px;
        }

        .logo-container h2 {
            color: #0A28D8;
            font-size: 22px;
            text-align: center;
        }
        .back-btn {
            position: absolute;
            padding: 8px 16px;
            background-color: #0A28D8;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .logo-container p {
            color: #999;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
   
<div class="container">
<a href="{{ route('employees.index') }}" class="back-btn">Back to Employees</a>
    <div class="logo-container">
            <img src="/images/psulogo.png" alt="Logo">
        </div>
        <h2>Monthly Report on Undertime and Absences</h2>

        <p class="header">
            For the Month of:
            {{
                \Carbon\Carbon::createFromDate(
                    now()->year,
                    request('month', 4),
                    1
                )->format('F Y')
            }}
        </p>

        <form action="{{ route('attendance.report') }}" method="GET">
            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="">All</option>
                <option value="Instructional" {{ request('role')=='Instructional'?'selected':'' }}>Instructional</option>
                <option value="Non-Instructional" {{ request('role')=='Non-Instructional'?'selected':'' }}>Non-Instructional</option>
            </select>

            <label for="month">Month:</label>
            <select name="month" id="month">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month')==$m?'selected':'' }}>
                        {{ \Carbon\Carbon::createFromDate(now()->year, $m, 1)->format('F') }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Filter</button>
            <a href="#" class="pagination-btn btn-download">Download</a>
        </form>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Undertime</th>
                    <th>Absences</th>
                    <th>Individual Info</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($records as $i => $r)
                <tr>
                    <td>{{ ($records->currentPage() - 1) * $records->perPage() + $i + 1 }}</td>
                    <td>{{ $r['name'] }}</td>
                    <td>{{ $r['undertime'] ?: '' }}</td>
                    <td>{{ $r['absences'] ?? '' }}</td>
                    <td><a href="#" class="pagination-btn btn-download">Show Data</a></td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            @if ($records->onFirstPage())
                <span class="pagination-btn disabled">First</span>
                <span class="pagination-btn disabled">Previous</span>
            @else
                <a href="{{ $records->url(1) }}" class="pagination-btn">First</a>
                <a href="{{ $records->previousPageUrl() }}" class="pagination-btn">Previous</a>
            @endif

            @foreach ($records->getUrlRange(max(1, $records->currentPage() - 2), min($records->lastPage(), $records->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="pagination-btn {{ $records->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            @if ($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}" class="pagination-btn">Next</a>
                <a href="{{ $records->url($records->lastPage()) }}" class="pagination-btn">Last</a>
            @else
                <span class="pagination-btn disabled">Next</span>
                <span class="pagination-btn disabled">Last</span>
            @endif
        </div>
    </div>
</body>
</html>
