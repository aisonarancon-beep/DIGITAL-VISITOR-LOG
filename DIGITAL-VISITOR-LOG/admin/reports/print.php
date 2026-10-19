<?php

require_once "../../includes/auth.php";
require_once "../../config/database.php";

$visitors = $conn->query("
    SELECT
        full_name,
        contact_number,
        purpose,
        person_to_visit,
        department,
        time_in,
        time_out,
        status
    FROM visitors
    ORDER BY time_in DESC
");

$totalVisitors = $conn->query("
    SELECT COUNT(*) AS total
    FROM visitors
")->fetch_assoc()["total"];

$todayVisitors = $conn->query("
    SELECT COUNT(*) AS total
    FROM visitors
    WHERE DATE(time_in) = CURDATE()
")->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Printable Visitor Report</title>

    <link
        rel="stylesheet"
        href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css"
    >

</head>

<body class="bg-white">

    <div class="max-w-7xl mx-auto p-8">

        <!-- REPORT HEADER -->

        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-gray-800">
                DIGITAL VISITOR LOG
            </h1>

            <p class="text-gray-600 mt-2">
                Visitor Records Report
            </p>

            <p class="text-sm text-gray-500 mt-1">
                Generated: <?= date("M d, Y h:i A") ?>
            </p>

        </div>


        <!-- SUMMARY -->

        <div class="grid grid-cols-2 gap-4 mb-8">

            <div class="border rounded-lg p-4">

                <p class="text-sm text-gray-500">
                    Total Visitors
                </p>

                <p class="text-2xl font-bold">
                    <?= $totalVisitors ?>
                </p>

            </div>


            <div class="border rounded-lg p-4">

                <p class="text-sm text-gray-500">
                    Visitors Today
                </p>

                <p class="text-2xl font-bold">
                    <?= $todayVisitors ?>
                </p>

            </div>

        </div>


        <!-- TABLE -->

        <table class="w-full border-collapse border border-gray-400">

            <thead>

                <tr class="bg-gray-100">

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Visitor
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Contact
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Purpose
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Person to Visit
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Department
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Time In
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Time Out
                    </th>

                    <th class="border border-gray-400 px-3 py-3 text-left text-sm">
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if ($visitors->num_rows > 0): ?>

                    <?php while ($visitor = $visitors->fetch_assoc()): ?>

                        <tr>

                            <td class="border border-gray-400 px-3 py-3 text-sm">
                                <?= htmlspecialchars($visitor["full_name"]) ?>
                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">
                                <?= htmlspecialchars($visitor["contact_number"]) ?>
                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">
                                <?= htmlspecialchars($visitor["purpose"]) ?>
                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">
                                <?= htmlspecialchars($visitor["person_to_visit"]) ?>
                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">
                                <?= htmlspecialchars($visitor["department"]) ?>
                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">

                                <?= date(
                                    "M d, Y h:i A",
                                    strtotime($visitor["time_in"])
                                ) ?>

                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">

                                <?php if (!empty($visitor["time_out"])): ?>

                                    <?= date(
                                        "M d, Y h:i A",
                                        strtotime($visitor["time_out"])
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>

                            <td class="border border-gray-400 px-3 py-3 text-sm">

                                <?= htmlspecialchars($visitor["status"]) ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            class="border border-gray-400 px-3 py-8 text-center"
                        >
                            No visitor records found.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>


        <!-- PRINT BUTTON -->

        <div class="mt-8 text-center print:hidden">

            <button
                onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
            >
                Print Report
            </button>

            <a
                href="index.php"
                class="inline-block bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 ml-2"
            >
                Back to Reports
            </a>

        </div>

    </div>

</body>

</html>