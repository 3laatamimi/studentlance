<?php
session_start();
session_destroy();
header('Location: /studentlance/index.php');
exit();
?>