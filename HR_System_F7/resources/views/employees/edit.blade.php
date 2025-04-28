<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>Edit Employee Data</h1>

<form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label for="name">Name:</label>
    <input type="text" name="name" value="{{ old('name', $employee->name) }}"> <br>

    <label for="id_number">Id:</label>
    <input type="text" name="id_number" value="{{ old('id_number', $employee->id_number) }}"> <br>

    <label for="college">Choose College:</label>
    <select name="college">
        <option value="">Select</option>
        <option value="coc" {{ old('college', $employee->college) == 'coc' ? 'selected' : '' }}>College of Computing</option>
        <!-- Add more options as needed -->
    </select> <br>

    <label for="classification">Choose Class Role:</label>
    <select name="classification">
        <option value="">Select</option>
        <option value="instructional" {{ old('classification', $employee->classification) == 'instructional' ? 'selected' : '' }}>Instructional</option>
        <option value="non-instructional" {{ old('classification', $employee->classification) == 'non-instructional' ? 'selected' : '' }}>Non-instructional</option>
    </select> <br>

    <label for="picture">Select image to upload:</label>
    <input type="file" name="picture" id="pictureInput" onchange="previewImage(event)"> <br>

    <div id="previewContainer" style="margin-top: 10px;">
        @if ($employee->picture)
            <img id="preview" src="{{ asset('storage/' . $employee->picture) }}" alt="Image Preview" style="max-width: 200px;">
        @else
            <img id="preview" src="#" alt="Image Preview" style="display: none; max-width: 200px;">
        @endif
    </div> <br>

    <button type="submit">Save</button>
</form>

<script>
    // Function to preview image when selected
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function() {
            preview.src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
</body>
</html>