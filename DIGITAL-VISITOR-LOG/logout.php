
<?php
session_start();

$_SESSION = [];
session_destroy();

header("Location: /DIGITAL-VISITOR-LOG/login.php");
exit();
?>
