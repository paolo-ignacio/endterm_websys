<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <h1>Add Employee data</h1>
    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="name">Name:</label>
        <input type="text" name="name"> <br>
        <label for="id_number">Id:</label>
        <input type="text" name="id_number"> <br>
        <label for="college">Choose College:</label>
        <select name="college" id="">
            <option value="">Select</option>
            <option value="coc">College of Computing</option>
           
        </select> <br>
        <label for="classification">Choose Class Role:</label>
        <select name="classification" id="">
            <option value="">Select</option>
            <option value="instructional">Instructional</option>
            <option value="instructional">Non-instructional</option>
        </select> <br>
        <label for="pciture"> Select image to upload:</label>
        <input type="file" name="picture" id="pictureInput" onchange="previewImage(event)"> <br>
        <div id="previewContainer" style="margin-top: 10px;">
        <img id="preview" src="#" alt="Image Preview" style="display: none; max-width: 200px;">
        </div> <br>
        <button type="submit">Save</button>
    </form>

    <script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>