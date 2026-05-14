<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: ../login.php?s=0');
exit;
