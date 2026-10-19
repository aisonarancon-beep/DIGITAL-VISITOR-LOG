
<?php

require_once "../../includes/auth.php";
require_once "../../config/database.php";


// GET SEARCH AND FILTER VALUES
 

$search = trim($_GET["search"] ?? "");
$status = $_GET["status"] ?? "";
$date = $_GET["date"] ?? "";


// BUILD VISITOR QUERY

$sql = "
    SELECT
        id,
        full_name,
        contact_number,
        purpose,
        person_to_visit,
        department,
        photo,
        time_in,
        time_out,
        status
    FROM visitors
    WHERE 1=1
";


// SEARCH VISITOR
if ($search != "") {

    $search = mysqli_real_escape_string($conn, $search);

    $sql .= "
        AND (
            full_name LIKE '%$search%'
            OR contact_number LIKE '%$search%'
            OR purpose LIKE '%$search%'
            OR person_to_visit LIKE '%$search%'
            OR department LIKE '%$search%'
        )
    ";
}


// FILTER BY STATUS
if ($status == "Inside" || $status == "Exited") {

    $status = mysqli_real_escape_string($conn, $status);

    $sql .= " AND status = '$status'";
}


// FILTER BY DATE
if ($date != "") {

    $date = mysqli_real_escape_string($conn, $date);

    $sql .= " AND DATE(time_in) = '$date'";
}


// SHOW LATEST VISITORS FIRST
$sql .= " ORDER BY time_in DESC";

$visitors = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visitors Records - Digital Visitor Log</title>

    <link
        rel="stylesheet"
        href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css"
    >

</head>

<body class="bg-gray-100">

<div class="flex min-h-screen w-full">

    <!-- SIDEBAR -->

    <aside class="w-64 flex-shrink-0 bg-blue-700 text-white">

        <div class="p-6">

            <h1 class="text-2xl font-bold">DIGITAL</h1>

            <h1 class="text-2xl font-bold">VISITOR LOG</h1>

            <p class="text-blue-200 text-sm mt-1">
                Admin Panel
            </p>

        </div>

        <!-- NAVIGATION -->

        <nav class="mt-4">

            <a
                href="../dashboard.php"
                class="block px-6 py-3 hover:bg-blue-800"
            >
                Dashboard
            </a>

           
            <a
                href="index.php"
                class="block px-6 py-3 bg-blue-800 font-medium"
            >
                Visitors Records
            </a>

            <a
                href="../reports/index.php"
                class="block px-6 py-3 hover:bg-blue-800"
            >
                Reports
            </a>

            <a
                href="../../logout.php"
                class="block px-6 py-3 hover:bg-blue-800"
            >
                Logout
            </a>

        </nav>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="flex-1 min-w-0">

        <!-- HEADER -->

        <header class="bg-white shadow-sm px-4 md:px-8 py-5">

            <div class="flex flex-wrap items-center justify-between gap-4">

                <div>

                    <h2 class="text-2xl font-bold text-gray-800">
                        Visitors Records
                    </h2>

                    <p class="text-gray-500 text-sm">
                        Manage registered visitor records
                    </p>

                </div>

                <a
                    href="create.php"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                    + Add Visitor
                </a>

            </div>

        </header>


        <!-- MARQUEE -->

        <div class="bg-blue-600 text-white py-2">

            <marquee>
                DIGITAL VISITOR LOG - VISITOR RECORDS MANAGEMENT
            </marquee>

        </div>


        <!-- PAGE CONTENT -->

        <div class="p-4 md:p-8 w-full min-w-0">

            <!-- SEARCH AND FILTER -->

            <div class="bg-white rounded-xl shadow p-5 md:p-6 mb-6">

                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Search and Filter Visitors
                </h3>

                <form method="GET" action="index.php">

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                        <!-- SEARCH -->

                        <div class="xl:col-span-2">

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                value="<?= htmlspecialchars($search) ?>"
                                placeholder="Name, contact, purpose..."
                                class="w-full min-w-0 border border-gray-300 rounded-lg px-4 py-3"
                            >

                        </div>


                        <!-- STATUS -->

                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full min-w-0 border border-gray-300 rounded-lg px-4 py-3 bg-white"
                            >

                                <option value="">All Status</option>

                                <option
                                    value="Inside"
                                    <?= $status == "Inside" ? "selected" : "" ?>
                                >
                                    Inside
                                </option>

                                <option
                                    value="Exited"
                                    <?= $status == "Exited" ? "selected" : "" ?>
                                >
                                    Exited
                                </option>

                            </select>

                        </div>


                        <!-- DATE -->

                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Visit Date
                            </label>

                            <input
                                type="date"
                                name="date"
                                value="<?= htmlspecialchars($date) ?>"
                                class="w-full min-w-0 border border-gray-300 rounded-lg px-4 py-3"
                            >

                        </div>

                    </div>


                    <!-- FILTER BUTTONS -->

                    <div class="flex flex-wrap gap-3 mt-5">

                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
                        >
                            Apply Filter
                        </button>

                        <a
                            href="index.php"
                            class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300"
                        >
                            Clear
                        </a>

                    </div>

                </form>

            </div>


            <!-- VISITOR TABLE -->

            <div class="bg-white rounded-xl shadow w-full min-w-0">

                <!-- TABLE HEADER -->

                <div class="px-4 md:px-6 py-5 border-b">

                    <h3 class="text-xl font-bold text-gray-800">
                        Visitor Records
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        View, edit, time out, or delete visitor records.
                    </p>

                </div>


                <!-- TABLE SCROLL AREA -->

                <div class="w-full min-w-0 overflow-x-auto">

                    <table class="w-full min-w-max text-left">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Photo
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Visitor
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Purpose
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Person to Visit
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Department
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Time In
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Time Out
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Status
                                </th>

                                <th class="px-3 py-4 text-sm font-semibold text-gray-600">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y">

                            <?php if ($visitors && mysqli_num_rows($visitors) > 0): ?>

                                <?php while ($visitor = mysqli_fetch_assoc($visitors)): ?>

                                    <tr class="hover:bg-gray-50">

                                        <!-- PHOTO -->

                                        <td class="px-3 py-3">

                                            <?php if (!empty($visitor["photo"])): ?>

                                                <img
                                                    src="../../uploads/visitors/<?= htmlspecialchars($visitor["photo"]) ?>"
                                                    alt="Visitor Photo"
                                                    class="w-12 h-12 object-cover rounded-lg border"
                                                >

                                            <?php else: ?>

                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                                                    No Photo
                                                </div>

                                            <?php endif; ?>

                                        </td>


                                        <!-- VISITOR -->

                                        <td class="px-3 py-3">

                                            <p class="font-medium text-gray-800 text-sm">
                                                <?= htmlspecialchars($visitor["full_name"]) ?>
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                <?= htmlspecialchars($visitor["contact_number"]) ?>
                                            </p>

                                        </td>


                                        <!-- PURPOSE -->

                                        <td class="px-3 py-3 text-sm text-gray-600">
                                            <?= htmlspecialchars($visitor["purpose"]) ?>
                                        </td>


                                        <!-- PERSON TO VISIT -->

                                        <td class="px-3 py-3 text-sm text-gray-600">
                                            <?= htmlspecialchars($visitor["person_to_visit"]) ?>
                                        </td>


                                        <!-- DEPARTMENT -->

                                        <td class="px-3 py-3 text-sm text-gray-600">
                                            <?= htmlspecialchars($visitor["department"]) ?>
                                        </td>


                                        <!-- TIME IN -->

                                        <td class="px-3 py-3 text-sm text-gray-600 whitespace-nowrap">

                                            <?= !empty($visitor["time_in"])
                                                ? date("M d, Y h:i A", strtotime($visitor["time_in"]))
                                                : "—" ?>

                                        </td>


                                        <!-- TIME OUT -->

                                        <td class="px-3 py-3 text-sm text-gray-600 whitespace-nowrap">

                                            <?php if (!empty($visitor["time_out"])): ?>

                                                <?= date("M d, Y h:i A", strtotime($visitor["time_out"])) ?>

                                            <?php else: ?>

                                                <span class="text-gray-400">—</span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- STATUS -->

                                        <td class="px-3 py-3">

                                            <?php if ($visitor["status"] == "Inside"): ?>

                                                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">
                                                    Inside
                                                </span>

                                            <?php else: ?>

                                                <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">
                                                    Exited
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- ACTIONS -->

                                        <td class="px-3 py-3">

                                            <div class="flex flex-wrap gap-2">

                                                <!-- EDIT -->

                                                <a
                                                    href="edit.php?id=<?= (int) $visitor["id"] ?>"
                                                    class="bg-blue-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-600"
                                                >
                                                    Edit
                                                </a>


                                                <!-- TIME OUT -->

                                                <?php if ($visitor["status"] == "Inside"): ?>

                                                    <a
                                                        href="../time_out.php?id=<?= (int) $visitor["id"] ?>"
                                                        class="bg-green-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-600"
                                                        onclick="return confirm('Time out this visitor?');"
                                                    >
                                                        Time Out
                                                    </a>

                                                <?php endif; ?>


                                                <!-- DELETE -->

                                                <a
                                                    href="delete.php?id=<?= (int) $visitor["id"] ?>"
                                                    class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600"
                                                    onclick="return confirm('Are you sure you want to delete this visitor record?');"
                                                >
                                                    Delete
                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="9"
                                        class="px-6 py-12 text-center text-gray-500"
                                    >
                                        No visitor records found. Try changing your search or filters.
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>
