<?php

require_once "config/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($username === "" || $password === "" || $confirm_password === "") {

        $error = "Please fill in all fields.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM admins WHERE username = ?"
        );

        $check->bind_param("s", $username);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $error = "Username already exists.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO admins (username, password) VALUES (?, ?)"
            );

            $stmt->bind_param(
                "ss",
                $username,
                $hashed_password
            );

            if ($stmt->execute()) {

                $success = "Admin account created successfully.";

            } else {

                $error = "Something went wrong.";

            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Digital Visitor Log</title>
    <link rel="stylesheet"href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4">

        <div class="bg-white rounded-xl shadow-md p-8">

            <div class="text-center mb-6">

                <h1 class="text-2xl font-bold text-blue-700">
                    DIGITAL VISITOR LOG
                </h1>

                <p class="text-gray-500 mt-2">
                    Create Administrator Account
                </p>

            </div>


            <?php if ($error): ?>

                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-5">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <?php if ($success): ?>

                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-5">

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>


            <form method="POST">

                <!-- Username -->
                <div class="mb-4">

                    <label class="block text-gray-700 font-medium mb-2">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        minlength="5"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3"
                        placeholder="Enter username">

                </div>


                <!-- Password -->
                <div class="mb-4">

                    <label class="block text-gray-700 font-medium mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3"
                        placeholder="Enter password">

                </div>


                <!-- Confirm Password -->
                <div class="mb-6">

                    <label class="block text-gray-700 font-medium mb-2">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        name="confirm_password"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3"
                        placeholder="Confirm password">

                </div>


                <button
                    type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-lg font-semibold">

                    CREATE ADMIN ACCOUNT

                </button>

            </form>


            <div class="text-center mt-5">

                <a
                    href="login.php"
                    class="text-blue-600 hover:text-blue-800">

                    ← Back to Login

                </a>

            </div>

        </div>

    </div>

</body>

</html>