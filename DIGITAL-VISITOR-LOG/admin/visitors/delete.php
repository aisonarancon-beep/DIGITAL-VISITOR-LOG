<?php

require_once "../../includes/auth.php";
require_once "../../config/database.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET["id"]);

// Get the visitor photo first
$stmt = $conn->prepare("SELECT photo FROM visitors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$visitor = $result->fetch_assoc();

$stmt->close();

if (!$visitor) {
    header("Location: index.php");
    exit;
}

// Delete the database record
$stmt = $conn->prepare("DELETE FROM visitors WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    // Delete the photo from the uploads folder
    if (!empty($visitor["photo"])) {

        $photo_path = "../../uploads/visitors/" . $visitor["photo"];

        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
}

$stmt->close();

header("Location: index.php");
exit;

?>