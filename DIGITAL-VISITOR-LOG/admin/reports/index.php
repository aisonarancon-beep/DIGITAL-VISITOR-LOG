<?php


require_once "../../includes/auth.php";
require_once "../../config/database.php";

$from = $_GET["from_date"] ?? "";
$to = $_GET["to_date"] ?? "";

$condition = " WHERE 1=1 ";

if ($from != "") {
    $from = mysqli_real_escape_string($conn, $from);
    $condition .= " AND DATE(time_in) >= '$from'";
}

if ($to != "") {
    $to = mysqli_real_escape_string($conn, $to);
    $condition .= " AND DATE(time_in) <= '$to'";
}

// Visitor statistics
$total = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM visitors $condition"
))["total"];

$inside = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM visitors WHERE status='Inside'"
))["total"];

$exited = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM visitors $condition AND status='Exited'"
))["total"];

$average = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) / NULLIF(COUNT(DISTINCT DATE(time_in)), 0) AS avg
     FROM visitors $condition"
))["avg"] ?? 0;

// Daily report
$daily = mysqli_query($conn, "
    SELECT DATE(time_in) AS visit_date, COUNT(*) AS total
    FROM visitors $condition
    GROUP BY DATE(time_in)
    ORDER BY visit_date DESC
    LIMIT 30
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visitor Reports</title>
    <link rel="stylesheet" href="/DIGITAL-VISITOR-LOG/assets/CSS/tailwind.min.css">
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-blue-700 text-white flex-shrink-0">
        <div class="p-6">
            <h1 class="text-2xl font-bold">DIGITAL VISITOR LOG</h1>
            <p class="text-blue-200 text-sm mt-2">Admin Panel</p>
        </div>

        <nav>
            <a href="../dashboard.php" class="block px-6 py-3 hover:bg-blue-800">Dashboard</a>
            <a href="../visitors/index.php" class="block px-6 py-3 hover:bg-blue-800">Visitors Records</a>
            <a href="index.php" class="block px-6 py-3 bg-blue-800">Visitor Reports</a>
            <a href="../../logout.php" class="block px-6 py-3 hover:bg-blue-800">Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 min-w-0">

        <header class="bg-white shadow px-8 py-5 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Visitor Reports</h2>
                <p class="text-sm text-gray-500">Visitor statistics and daily reports</p>
            </div>

            <a href="print.php?from_date=<?= urlencode($from) ?>&to_date=<?= urlencode($to) ?>"
               target="_blank"
               class="bg-blue-700 text-white px-4 py-2 rounded-lg">
                Print Report
            </a>
        </header>

        <div class="p-8">

            <!-- DATE FILTER -->
            <form method="GET" class="bg-white rounded-xl shadow p-6 mb-8">
                <h3 class="font-bold text-lg mb-4">Search by Date</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="date" name="from_date" value="<?= htmlspecialchars($from) ?>"
                           class="border rounded-lg p-3">

                    
                    <div class="flex gap-2">
                        <button class="bg-blue-700 text-white px-4 py-3 rounded-lg">Search</button>
                        <a href="index.php" class="bg-gray-200 px-4 py-3 rounded-lg">Reset</a>
                    </div>
                </div>
            </form>

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

                <div class="bg-white rounded-xl shadow p-6">
                    <p class="text-gray-500">Visitors in Period</p>
                    <h3 class="text-3xl font-bold mt-2"><?= $total ?></h3>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <p class="text-gray-500">Currently Inside</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2"><?= $inside ?></h3>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <p class="text-gray-500">Exited Visitors</p>
                    <h3 class="text-3xl font-bold text-gray-600 mt-2"><?= $exited ?></h3>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <p class="text-gray-500">Daily Average</p>
                    <h3 class="text-3xl font-bold text-blue-600 mt-2">
                        <?= round((float) $average, 1) ?>
                    </h3>
                </div>

            </div>

            <!-- DAILY REPORT TABLE -->
            <div class="bg-white rounded-xl shadow">

                <div class="p-6 border-b">
                    <h3 class="text-xl font-bold">Daily Visitor Report</h3>
                    <p class="text-sm text-gray-500 mt-1">Latest 30 recorded dates</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4">Date</th>
                                <th class="p-4">Number of Visitors</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            <?php if ($daily && mysqli_num_rows($daily) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($daily)): ?>
                                    <tr>
                                        <td class="p-4">
                                            <?= date("M d, Y", strtotime($row["visit_date"])) ?>
                                        </td>
                                        <td class="p-4 font-semibold text-blue-700">
                                            <?= $row["total"] ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="p-6 text-center text-gray-500">
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
```