<?php
//bu cagrılırsa iş biter
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
