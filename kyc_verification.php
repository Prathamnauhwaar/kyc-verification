<?php
// Database connection
$host = 'localhost';
$db = 'kyc_db';
$user = 'root'; // Change as needed
$pass = 'root'; // Change as needed

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$firstName = $email = $phoneNumber = $address = $state = $district = "";
$aadharPhoto = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phoneNumber = htmlspecialchars(trim($_POST['phone_number']));
    $address = htmlspecialchars(trim($_POST['address']));
    $state = htmlspecialchars(trim($_POST['state']));
    $district = htmlspecialchars(trim($_POST['district']));

    // Handle file upload
    if (isset($_FILES['aadhar_photo']) && $_FILES['aadhar_photo']['error'] == 0) {
        $targetDir = "uploads/";
        
        // Check if uploads drecory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create the directory if it does not exist
        }

        $targetFile = $targetDir . basename($_FILES["aadhar_photo"]["name"]);
        
        // Check file size (limit to 2MB)
        if ($_FILES["aadhar_photo"]["size"] > 2000000) {
            echo "Sorry, your file is too large.";
            exit;
        }

        // Allow certain file formats
        if (!in_array(strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
            echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
            exit;
        }

        // Upload file
        if (move_uploaded_file($_FILES["aadhar_photo"]["tmp_name"], $targetFile)) {
            $aadharPhoto = htmlspecialchars($targetFile);
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO kyc_users(first_name, email, phone_number, address, state, district, aadhar_photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstName, $email, $phoneNumber, $address, $state, $district, $aadharPhoto);

    if ($stmt->execute()) {
        echo "KYC details submitted successfully!";
    } else {
        echo "Error: " . $stmt->error; // Output detailed error message
    }

    $stmt->close();
}

// Fetch all KYC records for display
$result = $conn->query("SELECT * FROM kyc_users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f7f7f7;
        }
        .sidebar {
            width: 250px;
            background: #0e0e11;
            color: #fff;
            position: fixed;
            height: 100%;
            padding: 20px;
       }
        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 15px 0;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
        .main {
            margin-left: 270px;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 250;
            height: 300;
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .for-group select, .form-group input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .form-group input[type="checkbox"] {
            margin-right: 10px;
        }
        .btn {
            width: 100%;
            background: #4a46d6;
            color: #fff;
            padding: 10px;
            border: none;            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #3d39c6;
        }
    </style>
</head>
<body>
 <div class="sidebar">
        <h2>KYC Verification</h2>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Accounts</a></li>
            <li><a href="#">Deposit</a></li>
            <li><a href="#">Transfer</a></li>
            <li><a href="#">Withdraw</a></li>
            <li><a href="#">Affiliate</a></li>
            <li><a href="#">Leaderboards</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Legal Documents</a></li>
        </ul>
    </div> 

<div class="container">
    <h1>KYC Verification Form</h1>
    <form action="" method="post" enctype="multipart/form-data">
    <h2>
    <br>  <br>
     <label for="first_name">First Name:</label>
        <input type="text" id="first_name " name="first_name" required>
          <br/>
          <br>  <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br/>
        <br>  <br>
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>
        <br/>
        <br>  <br>
        <label for="address">Address:              </label>
        <input type="text" id="address" name="address" required>
        <br/>
        <br>  <br>
        <label for="state">State:</label>
       <input type="text" id="state" name="state" required>
       <br/>
       <br>  <br>
        <label for="district">District:</label>
        <input type="text" id="district" name="district" required>
        <br/>
        <br>  <br>
        <label for="aadhar_photo">Upload Aadhar Photo:</label>
        <input type="file" id="aadhar_photo" name="aadhar_photo" accept=".jpg,.jpeg,.png" required>
        <br/>
        <br>  <br>
        <input type="submit" value="Submit"></h2>
    </form>

    <h2>KYC Records</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Aadhar Photo</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                echo "<td><img src='" . htmlspecialchars($row['aadhar_photo']) ."' alt='Aadhar Photo'></td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>";
                echo "<form action='verify.php' method='post' style='display:inline;'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "' />
                        <input type='submit' class='button' value='Verify' />
                      </form>";
               echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found.</td></tr>";
        }
        ?>
    </table>
</div>

<?php
$conn->close();
?>
</body>
</html>