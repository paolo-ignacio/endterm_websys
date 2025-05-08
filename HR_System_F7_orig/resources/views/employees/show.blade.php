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
        padding: 40px 20px;
        background-color: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        flex-direction: column;
    }
    .wrapper {
    position: relative;
    width: 100%;
    max-width: 740px;
}

.back-btn {
    position: absolute;
    top: -20px;
    left: 0;
    padding: 8px 16px;
    background-color: #0A28D8;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

    .container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
        max-width: 700px;
        width: 100%;
    }

    .card-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        align-items: center;
        justify-content: space-between;
    }

    .qr-box {
        flex: 1 1 200px;
        text-align: center;
    }

    .info-box {
        flex: 2 1 300px;
    }

    .info-box h1 {
        color: #0A28D8;
        font-size: 26px;
        margin-bottom: 15px;
    }

    .info-box p {
        margin: 8px 0;
        font-size: 16px;
    }
    .employee-img {
    margin-top: 20px;
    }

    .employee-img img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ccc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>


    </style>
</head>
<body>
    

<div class="wrapper">
    <a href="{{ route('employees.index') }}" class="back-btn">Back to List</a>
<div class="container">
    <div class="card-layout">
        <div class="qr-box">
            {!! $qr !!}
            
        </div>
        <div class="info-box">
            @if ($employee->picture)
                <div class="employee-img">
                    <img src="{{ asset('images/' . $employee->picture) }}" alt="Employee Picture">
                </div>
            @endif
            <h1>{{ $employee->name }}</h1>
            <p><strong>ID:</strong> {{ $employee->id_number }}</p>
            <p><strong>Email:</strong> {{ $employee->classification }}</p>
            <p><strong>College:</strong> {{ $employee->college }}</p>
        </div>
    </div>
</div>
</div>
</body>
</html>