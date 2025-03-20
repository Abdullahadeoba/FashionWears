<?php
session_start();
session_destroy(); // Destroy session
header("Location: ../login.html"); // Go back to the correct location
exit();
?>
