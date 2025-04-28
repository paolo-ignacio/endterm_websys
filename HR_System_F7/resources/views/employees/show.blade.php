<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<a href="{{ route('employees.index') }}">Back to List</a>


<h1>{{ $employee->name }}</h1>
<p><strong> {{ $employee->id_number }}</strong></p>
<p><strong>Email:</strong> {{ $employee->classification }}</p>
<p><strong>Program:</strong> {{ $employee->college }}</p>

<div class="mt-4">
    <h4>QR Code</h4>
    <div>{!! $qr !!}</div> 
</div>
</body>
</html>