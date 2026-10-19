<?php
require_once "../../includes/auth.php";
require_once "../../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST["full_name"];
    $contact_number = $_POST["contact_number"];
    $purpose = $_POST["purpose"];
    $person_to_visit = $_POST["person_to_visit"];
    $department = $_POST["department"];

    $photo = $_FILES["photo"];

    if (
        empty($full_name) || empty($contact_number) ||
        empty($purpose) || empty($person_to_visit) ||
        empty($department) || $photo["error"] != 0
    ) {
        $error = "Please complete all fields and upload a photo.";
    } else {

        $folder = "../../uploads/visitors/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = uniqid() . ".jpg";
        $photoPath = $folder . $fileName;

        if (move_uploaded_file($photo["tmp_name"], $photoPath)) {

            $sql = "INSERT INTO visitors
            (full_name, contact_number, purpose, person_to_visit,
            department, photo, time_in, status)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Inside')";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssssss",
                $full_name,
                $contact_number,
                $purpose,
                $person_to_visit,
                $department,
                $fileName
            );

            if ($stmt->execute()) {
                header("Location: index.php");
                exit;
            } else {
                $error = "Failed to save visitor.";
                unlink($photoPath);
            }

            $stmt->close();

        } else {
            $error = "Photo upload failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Visitor - Digital Visitor Log</title>

    <link
        rel="stylesheet"
        href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css"
    >

</head>


<body class="bg-gray-100">


<div class="flex min-h-screen">


    <!-- SIDEBAR -->

    <aside class="w-64 bg-blue-700 text-white">

        <div class="p-6">

            <h1 class="text-2xl font-bold">
                DIGITAL
            </h1>

            <h1 class="text-2xl font-bold">
                VISITOR LOG
            </h1>

            <p class="text-blue-200 text-sm mt-1">
                Admin Panel
            </p>

        </div>


        <nav class="mt-4">


            <!-- DASHBOARD -->

            <a
                href="../dashboard.php"
                class="block px-6 py-3 hover:bg-blue-800"
            >
                Dashboard
            </a>


            <!-- VISITOR KIOSK -->

            <a
                href="../../index.php"
                class="block px-6 py-3 hover:bg-blue-800"
            >
                Visitor Kiosk
            </a>


            <!-- VISITOR RECORDS -->

            <a
                href="index.php"
                class="block px-6 py-3 bg-blue-800 font-medium"
            >
                Visitors Records
            </a>
            <!-- LOGOUT -->

            <a
                href="../../logout.php"
                class="block px-6 py-3 hover:bg-blue-800"
            >
                Logout
            </a>


        </nav>

    </aside>



    <!-- MAIN CONTENT -->

    <main class="flex-1">


        <!-- HEADER -->

        <header class="bg-white shadow-sm px-8 py-5">

            <div class="flex items-center justify-between">

                <div>

                    <h2 class="text-2xl font-bold text-gray-800">
                        Add Visitor
                    </h2>

                    <p class="text-gray-500 text-sm">
                        Create a new visitor record
                    </p>

                </div>


                <a
                    href="index.php"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300"
                >
                    Back to Records
                </a>

            </div>

        </header>



        <!-- MARQUEE -->

        <div class="bg-blue-600 text-white py-2">

            <marquee>

                DIGITAL VISITOR LOG - ADD NEW VISITOR

            </marquee>

        </div>



        <!-- FORM AREA -->

        <div class="p-8">


            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-8">


                <!-- ERROR MESSAGE -->

                <?php if ($error !== ""): ?>

                    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>



                <!-- FORM -->

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >


                    <!-- FULL NAME -->

                    <div class="mb-5">

                        <label
                            for="full_name"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?= htmlspecialchars($full_name) ?>"
                            placeholder="Enter visitor full name"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>



                    <!-- CONTACT NUMBER -->

                    <div class="mb-5">

                        <label
                            for="contact_number"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Contact Number
                        </label>

                        <input
                            type="text"
                            id="contact_number"
                            name="contact_number"
                            value="<?= htmlspecialchars($contact_number) ?>"
                            placeholder="Enter contact number"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>



                    <!-- PURPOSE -->

                    <div class="mb-5">

                        <label
                            for="purpose"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Purpose of Visit
                        </label>

                        <input
                            type="text"
                            id="purpose"
                            name="purpose"
                            value="<?= htmlspecialchars($purpose) ?>"
                            placeholder="Example: Meeting, Inquiry, Delivery"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>



                    <!-- PERSON TO VISIT -->

                    <div class="mb-5">

                        <label
                            for="person_to_visit"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Person to Visit
                        </label>

                        <input
                            type="text"
                            id="person_to_visit"
                            name="person_to_visit"
                            value="<?= htmlspecialchars($person_to_visit) ?>"
                            placeholder="Enter name of person to visit"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>



                    <!-- DEPARTMENT -->

                    <div class="mb-5">

                        <label
                            for="department"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Department
                        </label>

                        <input
                            type="text"
                            id="department"
                            name="department"
                            value="<?= htmlspecialchars($department) ?>"
                            placeholder="Enter department"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>



                    <!-- PHOTO -->

                    <div class="mb-8">

                        <label
                            for="photo"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Visitor Photo
                        </label>

                        <input
                            type="file"
                            id="photo"
                            name="photo"
                            accept="image/jpeg,image/png,image/jpg"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white"
                        >

                        <p class="text-xs text-gray-500 mt-2">

                            JPG or PNG only. Maximum file size: 5MB.

                        </p>

                    </div>



                    <!-- BUTTONS -->

                    <div class="flex justify-end gap-3">


                        <a
                            href="index.php"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            Save Visitor
                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>


</div>


</body>

</html>