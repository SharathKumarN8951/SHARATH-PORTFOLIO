<?php
// logout.php
session_start();

/* Remove all session data */
session_unset();
session_destroy();

/* 🔒 Prevent cached pages after logout */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* Redirect to admin login (or home if you prefer) */
header("Location: admin_login.php");
exit;
