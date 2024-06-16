<?php
session_start();
session_destroy();
header('Location: http://localhost/eduwork.local/index.php');
exit();
