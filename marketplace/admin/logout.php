<?php
require_once '../includes/functions.php';
unset($_SESSION['admin_id']);
unset($_SESSION['cart']);
redirect('../index.php');
