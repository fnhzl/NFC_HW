<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'superadmin_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$notification_message = "";
$notification_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
        $role = $conn->real_escape_string($_POST['role']);
        $gender = $conn->real_escape_string($_POST['gender']);
        $empname = strtoupper($conn->real_escape_string($_POST['empname']));
        $address = $conn->real_escape_string($_POST['address']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);

        $sql = "INSERT INTO users (username, empname, gender, email, address, phone_number, password, role) 
                VALUES ('$username', '$empname', '$gender', '$email', '$address', '$phone_number', '$password', '$role')";

        if ($conn->query($sql) === TRUE) {
            $notification_message = "New user added successfully!";
            $notification_type = "success";
        } else {
            $notification_message = "Error: " . $conn->error;
            $notification_type = "error";
        }
    } elseif (isset($_POST['edit_user'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $role = $conn->real_escape_string($_POST['role']);
        $empname = $conn->real_escape_string($_POST['empname']);
        $gender = $conn->real_escape_string($_POST['gender']);
        $address = $conn->real_escape_string($_POST['address']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);

        $sql = "UPDATE users SET username = '$username', empname = '$empname', gender = '$gender', email = '$email', address = '$address', phone_number = '$phone_number', role = '$role' WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            $notification_message = "User updated successfully!";
            $notification_type = "success";
        } else {
            $notification_message = "Error: " . $conn->error;
            $notification_type = "error";
        }
    }
}

if (isset($_GET['delete_id'])) {
    $id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM users WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        $notification_message = "User deleted successfully!";
        $notification_type = "success";
    } else {
        $notification_message = "Error: " . $conn->error;
        $notification_type = "error";
    }
}

$sql = "SELECT id, username, empname, gender, email, address, phone_number, role FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="adminmain.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
</head>
<body>
    <div class="top-nav-section">
        <div class="menu-nav-bar">
            <nav>
                <ul>
                    <li><a href="adminmain.php">Menu</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="adminmain.php">Dummy</a></li>
                    <li><img src="logo.png" id="logo" /> </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="left-nav-section">
        <nav>
            <!-- Left Nav content -->
        </nav>
    </div>

    <h1>Welcome, Superadmin</h1>

    <div class="business-card-table">
        <!-- Add User Form -->
        <h2>Add New User</h2>
        <form method="POST" action="" style="width: 100%">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="empname" placeholder="Employee Name" required>
            <select name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="phone_number" placeholder="Phone Number" pattern="\+60[0-9]{8,9}" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>

        <!-- User List -->
        <h2>User List</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['empname'] . "</td>";
            echo "<td>" . $row['gender'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['address'] . "</td>";
            echo "<td>" . $row['phone_number'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "<td>";
            echo "<form method='POST' action='' style='display:inline;'>";
            echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
            echo "<input type='text' name='username' placeholder='Username' value='" . $row['username'] . "' required>";
            echo "<input type='text' name='empname' placeholder='Name' value='" . $row['empname'] . "' required>";
            echo "<select name='gender' required>";
            echo "<option value='Male'" . ($row['gender'] == 'Male' ? ' selected' : '') . ">Male</option>";
            echo "<option value='Female'" . ($row['gender'] == 'Female' ? ' selected' : '') . ">Female</option>";
            echo "</select>";
            echo "<input type='email' name='email' placeholder='Email' value='" . $row['email'] . "' required>";
            echo "<input type='text' name='address' placeholder='Address' value='" . $row['address'] . "' required>";
            echo "<input type='text' name='phone_number' placeholder='Phone Number' pattern='\+60[0-9]{8,9}' value='" . $row['phone_number'] . "' required>";
            echo "<select name='role' required>";
            echo "<option value='admin'" . ($row['role'] == 'admin' ? ' selected' : '') . ">Admin</option>";
            echo "<option value='user'" . ($row['role'] == 'user' ? ' selected' : '') . ">User</option>";
            echo "</select>";
            echo "<button type='submit' name='edit_user'>Edit</button>";
            echo "</form>";
            echo "<a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No users found</td></tr>";
    }
    ?>
            </tbody>
        </table>
    </div>

    <!-- Notification Popup -->
    <div class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p id="notification-message"></p>
            <ul class="cd-buttons">
                <li><a href="#0" class="cd-popup-close">Close</a></li>
            </ul>
            <a href="#0" class="cd-popup-close img-replace">Close</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="admin.js"></script>
    <script>
        var notificationMessage = "<?php echo addslashes($notification_message); ?>";
        var notificationType = "<?php echo $notification_type; ?>";

        $(document).ready(function() {
            if (notificationMessage) {
                $('#notification-message').text(notificationMessage);
                $('.cd-popup').addClass('is-visible');
            }

            $('.cd-popup-close').on('click', function(event){
                event.preventDefault();
                $('.cd-popup').removeClass('is-visible');
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
