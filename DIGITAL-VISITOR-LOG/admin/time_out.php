
<?php

session_start();

require_once "../config/database.php";

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

// Get visitor ID
$id = (int) ($_GET["id"] ?? 0);

// Check ID
if ($id <= 0) {
    header("Location: visitors/index.php");
    exit;
}

// Update visitor status and time out
$sql = "UPDATE visitors
        SET time_out = CURRENT_TIMESTAMP,
            status = 'Exited'
        WHERE id = $id
        AND status = 'Inside'";

if (mysqli_query($conn, $sql)) {

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION["visitor_message"] =
            "Visitor has been timed out successfully.";
        $_SESSION["visitor_message_type"] = "success";
    } else {
        $_SESSION["visitor_message"] =
            "Visitor was already timed out or could not be found.";
        $_SESSION["visitor_message_type"] = "error";
    }

} else {

    $_SESSION["visitor_message"] =
        "Unable to time out visitor.";
    $_SESSION["visitor_message_type"] = "error";
}

// Return to Visitors Records
header("Location: visitors/index.php");
exit;

?>
