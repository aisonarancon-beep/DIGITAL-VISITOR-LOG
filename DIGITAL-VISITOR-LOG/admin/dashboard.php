<?php

    require_once "../includes/auth.php";
    require_once "../config/database.php";


    // DASHBOARD COUNTS


    // Total visitors
    $result = $conn->query("SELECT COUNT(*) AS total FROM visitors");
    $totalVisitors = $result->fetch_assoc()["total"];

    // Currently inside
    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM visitors
        WHERE status = 'Inside'
    ");
    $currentInside = $result->fetch_assoc()["total"];

    // Total exited
    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM visitors
        WHERE status = 'Exited'
    ");
    $totalExited = $result->fetch_assoc()["total"];

    // Today's visitors
    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM visitors
        WHERE DATE(time_in) = CURDATE()
    ");
    $todayVisitors = $result->fetch_assoc()["total"];


    // GET VISITOR RECORDS


    $visitors = $conn->query("
        SELECT id, full_name, contact_number, purpose,
            person_to_visit, department, photo,
            time_in, time_out, status
        FROM visitors
        ORDER BY time_in DESC
    ");

    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Digital Visitor Log</title>

    <link rel="stylesheet"
          href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css">
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

            <a href="dashboard.php"
               class="block px-6 py-3 bg-blue-800 font-medium">
                Dashboard
            </a>

            

            <a href="/DIGITAL-VISITOR-LOG/admin/visitors/index.php"
               class="block px-6 py-3 hover:bg-blue-800">
                Visitors Records
            </a>

            <a href="/DIGITAL-VISITOR-LOG/admin/reports/index.php"
               class="block px-6 py-3 hover:bg-blue-800">
                Reports
            </a>

            <a href="../logout.php"
               class="block px-6 py-3 hover:bg-blue-800">
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
                        Dashboard
                    </h2>

                    <p class="text-gray-500 text-sm">
                        Visitor monitoring and records
                    </p>
                </div>

                <a href="../index.php"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Open Visitor Kiosk
                </a>

            </div>

        </header>


        <!-- CONTENT -->
        <div class="p-4 md:p-8 w-full min-w-0">

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

                <div class="bg-pink-500 rounded-xl shadow p-5">
                    <p class="text-white text-sm">Total Visitors</p>
                    <h3 class="text-3xl font-bold text-white mt-2">
                        <?= $totalVisitors ?>
                    </h3>
                </div>

                <div class="bg-red-500 rounded-xl shadow p-5">
                    <p class="text-white text-sm">Currently Inside</p>
                    <h3 class="text-3xl font-bold text-white mt-2">
                        <?= $currentInside ?>
                    </h3>
                </div>

                <div class="bg-yellow-500 rounded-xl shadow p-5">
                    <p class="text-white text-sm">Exited Visitors</p>
                    <h3 class="text-3xl font-bold text-white mt-2">
                        <?= $totalExited ?>
                    </h3>
                </div>

                <div class="bg-green-500 rounded-xl shadow p-5">
                    <p class="text-white text-sm">Today's Visitors</p>
                    <h3 class="text-3xl font-bold text-white mt-2">
                        <?= $todayVisitors ?>
                    </h3>
                </div>

            </div>


            <!-- VISITOR TABLE -->
            <div class="bg-white rounded-xl shadow w-full min-w-0">

                <!-- TABLE HEADER -->
                <div class="px-4 md:px-6 py-5 border-b">

                    <h3 class="text-xl font-bold text-gray-800">
                        Visitor Records
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Latest visitor activity
                    </p>

                </div>


                <!-- SCROLL TABLE ONLY, NOT THE WHOLE PAGE -->
                <div class="w-full min-w-0 overflow-x-auto">

                    <table class="w-full min-w-max text-left">

                        <thead class="bg-gray-50">

                            <tr>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Photo</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Visitor</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Purpose</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Person to Visit</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Department</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Time In</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Time Out</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Status</th>
                                <th class="px-3 py-4 text-xs font-semibold text-gray-600">Action</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y">

                            <?php if ($visitors && $visitors->num_rows > 0): ?>

                                <?php while ($visitor = $visitors->fetch_assoc()): ?>

                                    <tr class="hover:bg-gray-50">

                                        <!-- PHOTO -->
                                        <td class="px-3 py-3">
                                            <?php if (!empty($visitor["photo"])): ?>

                                                <img
                                                    src="../uploads/visitors/<?= htmlspecialchars($visitor["photo"]) ?>"
                                                    alt="Visitor Photo"
                                                    class="w-10 h-10 object-cover rounded-lg border"
                                                >

                                            <?php else: ?>

                                                <span class="text-gray-400 text-xs">
                                                    No photo
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                        <!-- NAME AND CONTACT -->
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
                                            <?= date("M d, Y h:i A", strtotime($visitor["time_in"])) ?>
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

                                            <?php if ($visitor["status"] === "Inside"): ?>

                                                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">
                                                    Inside
                                                </span>

                                            <?php else: ?>

                                                <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">
                                                    Exited
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <!-- ACTION -->
                                        <td class="px-3 py-3 whitespace-nowrap">

                                            <?php if ($visitor["status"] === "Inside"): ?>

                                                <a
                                                    href="time_out.php?id=<?= (int) $visitor["id"] ?>"
                                                    class="inline-block bg-red-500 text-white px-3 py-2 rounded-lg text-xs hover:bg-red-600"
                                                    onclick="return confirm('Time out this visitor?');"
                                                >
                                                    Time Out
                                                </a>

                                            <?php else: ?>

                                                <span class="text-gray-400 text-xs">
                                                    Completed
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="9"
                                        class="px-6 py-12 text-center text-gray-500">
                                        No visitor records found.
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
