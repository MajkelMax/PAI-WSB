<?php
require_once 'includes/db_config.php';

session_destroy();
header('Location: login.php');
exit();