<?php
session_start();
session_destroy();
header("Location: npontu_login.php");
exit();