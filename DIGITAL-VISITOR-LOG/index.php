
<?php
session_start();
require_once "config/database.php";

// Show message
$message = $_SESSION["visitor_message"] ?? "";
$message_type = $_SESSION["message_type"] ?? "";

unset($_SESSION["visitor_message"]);
unset($_SESSION["message_type"]);

// Register visitor
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $contact_number = trim($_POST["contact_number"] ?? "");
    $person_to_visit = trim($_POST["person_to_visit"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $purpose = trim($_POST["purpose"] ?? "");
    $visitor_photo = $_POST["visitor_photo"] ?? "";

    // Check required fields
    if (
        $full_name == "" ||
        $contact_number == "" ||
        $person_to_visit == "" ||
        $department == "" ||
        $purpose == ""
    ) {
        $_SESSION["visitor_message"] = "Please complete all fields.";
        $_SESSION["message_type"] = "error";

    } elseif ($visitor_photo == "") {
        $_SESSION["visitor_message"] = "Please take your photo.";
        $_SESSION["message_type"] = "error";

    } else {

        // Convert photo into image data
        $photo_data = str_replace(
            "data:image/png;base64,",
            "",
            $visitor_photo
        );

        $photo_data = base64_decode($photo_data, true);

        if ($photo_data === false) {
            $_SESSION["visitor_message"] = "Invalid photo. Please retake it.";
            $_SESSION["message_type"] = "error";

        } else {

            // Create photo folder
            $folder = "uploads/visitors/";

            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            // Save photo
            $photo_name = uniqid("visitor_") . ".png";
            $photo_path = $folder . $photo_name;

            if (file_put_contents($photo_path, $photo_data) === false) {
                $_SESSION["visitor_message"] = "Unable to save photo.";
                $_SESSION["message_type"] = "error";

            } else {

                // Prepare values for database
                $full_name = mysqli_real_escape_string($conn, $full_name);
                $contact_number = mysqli_real_escape_string($conn, $contact_number);
                $person_to_visit = mysqli_real_escape_string($conn, $person_to_visit);
                $department = mysqli_real_escape_string($conn, $department);
                $purpose = mysqli_real_escape_string($conn, $purpose);
                $photo_name = mysqli_real_escape_string($conn, $photo_name);

                // Save visitor information
                $sql = "INSERT INTO visitors
                    (full_name, contact_number, purpose, person_to_visit,
                     department, photo, time_in, status)
                    VALUES
                    ('$full_name', '$contact_number', '$purpose',
                     '$person_to_visit', '$department', '$photo_name',
                     NOW(), 'Inside')";

                if (mysqli_query($conn, $sql)) {
                    $_SESSION["visitor_message"] = "Visit registered successfully.";
                    $_SESSION["message_type"] = "success";
                } else {
                    unlink($photo_path);
                    $_SESSION["visitor_message"] = "Unable to register visitor.";
                    $_SESSION["message_type"] = "error";
                }
            }
        }
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Visitor Log</title>

    <link rel="stylesheet" href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css">
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-blue-700 text-white py-5">
        <marquee behavior="alternate" direction="left" class="text-3xl font-semibold">
            Digital Visitor Log - Please Register Your Visit
        </marquee>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-6">

        <!-- Messages -->
        <?php if ($message != ""): ?>
            <div class="<?php echo $message_type == 'success' ? 'bg-green-100 text-green-700 border-green-300' : 'bg-red-100 text-red-700 border-red-300'; ?> border rounded-lg px-4 py-3 mb-5">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Admin Login -->
        <div class="text-right mb-5">
            <a href="login.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                Admin Login
            </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- Left Information -->
            <div class="bg-blue-700 text-white rounded-xl shadow-md p-8">

                <p class="text-blue-200 text-sm uppercase">Visitor Check-In</p>

                <h1 class="text-3xl font-bold mt-2">Register Your Visit</h1>

                <p class="text-blue-100 mt-4">
                    Please provide your information and take a photo before entering the premises.
                </p>

                <div class="mt-10 space-y-6">

                    <div class="flex gap-4">
                        <div class="bg-white text-blue-700 rounded-full w-9 h-9 flex items-center justify-center font-bold">1</div>
                        <div>
                            <h3 class="font-semibold">Take Your Photo</h3>
                            <p class="text-blue-100 text-sm">Use the camera to capture your visitor photo.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="bg-white text-blue-700 rounded-full w-9 h-9 flex items-center justify-center font-bold">2</div>
                        <div>
                            <h3 class="font-semibold">Enter Your Information</h3>
                            <p class="text-blue-100 text-sm">Fill in the required visitor details.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="bg-white text-blue-700 rounded-full w-9 h-9 flex items-center justify-center font-bold">3</div>
                        <div>
                            <h3 class="font-semibold">Register Your Visit</h3>
                            <p class="text-blue-100 text-sm">Submit the form to record your time in.</p>
                        </div>
                    </div>

                </div>

                <div class="mt-10 border border-blue-500 rounded-lg p-4">
                    <p class="text-blue-100 text-sm">
                        Your visitor information will be recorded in the Digital Visitor Log.
                    </p>
                </div>
            </div>

            <!-- Camera -->
            <div class="bg-white rounded-xl shadow-md p-6">

                <h2 class="text-xl font-bold text-gray-800">Visitor Photo</h2>

                <p class="text-sm text-gray-500 mt-1 mb-5">
                    Capture your photo using the camera.
                </p>

                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <video id="camera" autoplay playsinline class="hidden w-full rounded-lg"></video>

                    <canvas id="photoCanvas" class="hidden w-full"></canvas>

                    <div id="cameraMessage" class="text-white text-center py-16">
                        Click START CAMERA to begin.
                    </div>
                </div>

                <div class="space-y-3 mt-5">

                    <button type="button" id="startCamera"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                        START CAMERA
                    </button>

                    <button type="button" id="takePhoto"
                        class="hidden w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                        TAKE PHOTO
                    </button>

                    <button type="button" id="retakePhoto"
                        class="hidden w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-semibold">
                        RETAKE PHOTO
                    </button>

                </div>

                <p class="text-center text-xs text-gray-400 mt-4">
                    Make sure your face is clearly visible.
                </p>
            </div>

            <!-- Visitor Form -->
            <div class="bg-white rounded-xl shadow-md p-6">

                <h2 class="text-xl font-bold text-gray-800">Visitor Information</h2>

                <p class="text-sm text-gray-500 mt-1 mb-5">
                    Enter your information below.
                </p>

                <form method="POST">

                    <input type="hidden" name="visitor_photo" id="visitorPhoto">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="full_name" required placeholder="Enter full name"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                        <input type="number" name="contact_number" required maxlength="11" placeholder="09XXXXXXXXX"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Person to Visit</label>
                        <input type="text" name="person_to_visit" required placeholder="Who are you visiting?"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Department</label>

                        <select name="department" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:outline-none focus:border-blue-500">
                            <option value="">Select Department</option>
                            <option value="Administration">Administration</option>
                            <option value="Accounting">Accounting</option>
                            <option value="Human Resources">Human Resources</option>
                            <option value="IT Department">IT Department</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Purpose of Visit</label>
                        <textarea name="purpose" rows="3" required placeholder="Why are you visiting?"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white py-4 rounded-lg font-bold text-lg">
                        REGISTER VISIT
                    </button>

                </form>
            </div>
        </div>
    </main>


    <script src="/DIGITAL-VISITOR-LOG/assets/JS/camera.js"></script>
</body>
</html>