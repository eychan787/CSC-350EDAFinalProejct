<?php
// logs out the user by destroying the session
session_start();
session_destroy();
header("Location: login.html");
exit;
?>
