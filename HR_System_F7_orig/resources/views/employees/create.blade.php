<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body,
        html {
            height: 100%;
            width: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
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

        .logo-container p {
            color: #999;
            font-size: 14px;
            text-align: center;
        }

        .form-box {
            background-color: #ffffff;
            color: #FFDA27;
            padding: 20px;
            border-radius: 12px;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #FFDA27;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 218, 39, 0.5); /* Optional: a soft glow effect */
        }

        .form-box h2 {
            font-size: 24px;
            color: #0A28D8;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            color:#FFDA27;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #0A28D8;
            border-radius: 8px;
            background: #fff;
            color: #333;
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #0A28D8;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            color: #f2f2f2;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #FFDA27;
        }

        #preview {
            display: none;
            max-width: 100%;
            margin-top: 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <img src="/images/psulogo.png" alt="Logo">
            <h2>Pangasinan State University - Urdaneta City Campus</h2>
            <p>Region's Premier University of Choice</p>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="form-box">
            @csrf
            <h2>Add Employee Data</h2>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Enter name">
                @error('name') 
                    <span style="color:red">{{ $message }}</span> 
                @enderror
            </div>

            <div class="form-group">
                <label for="id_number">ID:</label>
                <input type="text" name="id_number" placeholder="Enter ID" >
                @error('id_number') 
                    <span style="color:red">{{ $message }}</span> 
                @enderror
            </div>

            <div class="form-group">
                <label for="college">Choose College:</label>
                <select name="college" required>
                    <option value="">Select</option>
                    <option value="College of Computing">College of Computing</option>
                    <option value="College of Teacher Education">College of Teacher Education</option>
                    <option value="College of Engineering">College of Engineering</option>
                    <option value="College of Architecture">College of Architecture</option>
                </select>
                @error('college') 
                    <span style="color:red">{{ $message }}</span> 
                @enderror
            </div>

            <div class="form-group">
                <label for="classification">Choose Class Role:</label>
                <select name="classification" required>
                    <option value="">Select</option>
                    <option value="Instructional">Instructional</option>
                    <option value="Non-instructional">Non-instructional</option>
                </select>
                @error('classification') 
                    <span style="color:red">{{ $message }}</span> 
                @enderror
            </div>

            <div class="form-group">
                <label for="picture">Upload Image:</label>
                <input type="file" name="picture" id="pictureInput" onchange="previewImage(event)">
                <img id="preview" alt="Image Preview">
                @error('picture') 
                    <span style="color:red">{{ $message }}</span> 
                @enderror
            </div>

            <button type="submit" class="login-btn">Save</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>