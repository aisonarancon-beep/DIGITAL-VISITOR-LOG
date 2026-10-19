
<?php
session_start();
require_once "config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT * FROM admins WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {

        if (password_verify($password, $admin["password"])) {

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            header("Location: /DIGITAL-VISITOR-LOG/admin/dashboard.php");
            exit;

        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "Account not found. Please register first.";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Digital Visitor Log</title>

    <link rel="stylesheet" href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css">
</head>

<body class="bg-gray-200 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-xl shadow-md p-8">

            <h1 class="text-2xl font-bold text-blue-700 text-center">
                DIGITAL VISITOR LOG
            </h1>

            <p class="text-gray-500 text-center mt-2 mb-6">
                Administrator Login
            </p>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 p-2 rounded text-sm mt-3 text-center">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label class="block mb-2">Username</label>
                <input
                    type="text"
                    name="username"
                    required
                    class="w-full border rounded-lg p-3 mb-4"
                    placeholder="Enter username">

                <label class="block mb-2">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border rounded-lg p-3 mb-6"
                    placeholder="Enter password">

                <button
                    type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white p-3 rounded-lg">
                    LOGIN
                </button>

            </form>

            <div class="text-center mt-5">
                <a href="index.php" class="text-blue-600">
                    Back to Home
                </a>
            </div>

            <div class="text-center mt-4">
                <a href="register.php" class="text-blue-600">
                    Don't Have an Account? Register Here
                </a>
            </div>

        </div>
    </div>

</body>
</html>
