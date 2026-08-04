<?php 
require_once '../config/database.php';
requireLogin('organizer');

$currentPage = 'vision';
require_once '../views/organizer/vision_view.php';
?>