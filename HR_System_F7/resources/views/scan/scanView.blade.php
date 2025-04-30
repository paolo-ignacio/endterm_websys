<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <style>
        #reader {
            width: 100%;
            max-width: 500px;
            margin: auto;
        }
        #employee-info {
            display: none;
            margin-top: 20px;
        }
        img.photo {
            max-width: 100px;
            border-radius: 10px;
        }
        #error-message {
            color: red;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<h2>psu logo</h2>
<h1>Pangasinan State University - Urdaneta City Campus</h1>
<h2>Daily Attendance Log</h2>
<button onclick="startScanner()">Start Scan</button>
<div id="error-message"></div>

<div id="reader" style="display: none;"></div>

<div id="employee-info">
    <h2>Employee Attendance</h2>
    <img id="empPhoto" class="photo" src="" alt="Employee Photo"><br>
    <strong>Name:</strong> <span id="empName"></span><br>
    <strong>ID Number:</strong> <span id="empID"></span><br>
    <strong>Classification:</strong> <span id="empClass"></span><br>
    <strong>College:</strong> <span id="empCollege"></span><br>
    <hr>
    <?php 
    $date = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $currentDate = $date->format('F j, Y');  // March 20, 2025
    echo $currentDate . "<br>";

    ?>
 
    <strong>AM Time In:</strong> <span id="amIn"></span><br>
    <strong>AM Time Out:</strong> <span id="amOut"></span><br>
    <strong>PM Time In:</strong> <span id="pmIn"></span><br>
    <strong>PM Time Out:</strong> <span id="pmOut"></span><br>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>

       // Function to display the current date
       function displayCurrentDate() {
        const today = new Date();
        const date = today.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('current-date').textContent = 'Date: ' + date;
    }

    // Call the function to display the date
 
    function startScanner() {
        document.getElementById("error-message").textContent = "";
        document.getElementById("reader").style.display = "block";

        const html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                html5QrCode.stop();
                try {
                    const data = JSON.parse(decodedText);
                    sendToServer(data.id_number);
                } catch (e) {
                    document.getElementById("error-message").textContent = "Invalid QR code content.";
                }
            },
            (errorMessage) => {
                console.warn(errorMessage);
            }
        );
    }

    function sendToServer(idNumber) {
        fetch('/save-attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id_number: idNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.employee) {
                document.getElementById('empPhoto').src = data.employee.picture_path || '';
                document.getElementById('empName').textContent = data.employee.name || '—';
                document.getElementById('empID').textContent = data.employee.id_number || '—';
                document.getElementById('empClass').textContent = data.employee.classification || '—';
                document.getElementById('empCollege').textContent = data.employee.college || '—';

                document.getElementById('amIn').textContent = data.attendance.am_time_in || '—';
                document.getElementById('amOut').textContent = data.attendance.am_time_out || '—';
                document.getElementById('pmIn').textContent = data.attendance.pm_time_in || '—';
                document.getElementById('pmOut').textContent = data.attendance.pm_time_out || '—';

                document.getElementById('employee-info').style.display = 'block';
                document.getElementById('error-message').textContent = "";
            } else {
                document.getElementById("error-message").textContent = data.message || "Employee not found.";
            }
        });
    }
</script>
</body>
</html>
