<?php
require_once '../config/database.php';
requireLogin('organizer');

$currentPage = 'terminal';
require_once '../views/organizer/terminal_view.php';
?>