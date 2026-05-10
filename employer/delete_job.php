<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\JobRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$userId = Session::userId();
$jobId  = (int)($_GET['id'] ?? 0);

if ($jobId > 0) {
    JobRepository::delete($jobId, $userId);
}
header('Location: ../employer/dashboard.php');
exit;
