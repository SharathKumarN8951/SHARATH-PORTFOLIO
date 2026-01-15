<?php
// contact_process.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require "config.php";

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Always treat email as lowercase (matches your small-letters rule)
    $email = strtolower($email);

    // Basic empty check
    if ($name === "" || $email === "" || $subject === "" || $message === "") {
        header("Location: contact.php?status=error");
        exit;
    }

    // 1) Must be a valid general email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Invalid email structure like "abc@", "abc@com", etc.
        header("Location: contact.php?status=error");
        exit;
    }

    // 2) Must be ONLY small letters Gmail (e.g., sharath123@gmail.com)
    if (!preg_match('/^[a-z0-9._%+-]+@gmail\.com$/', $email)) {
        // Not lowercase gmail format
        header("Location: contact.php?status=error");
        exit;
    }

    // 3) DNS MX check for the domain (gmail.com)
    //    This confirms the domain can receive emails
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        header("Location: contact.php?status=error");
        exit;
    }

    list($user, $domain) = $parts;

    if (!checkdnsrr($domain, "MX")) {
        // Domain has no mail server records → very likely invalid
        header("Location: contact.php?status=error");
        exit;
    }

    // If we reach here, email passed all checks → save to DB
    $stmt = $conn->prepare(
        "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: contact.php?status=success");
            exit;
        } else {
            $stmt->close();
            $conn->close();
            header("Location: contact.php?status=error");
            exit;
        }
    } else {
        $conn->close();
        header("Location: contact.php?status=error");
        exit;
    }
} else {
    header("Location: contact.php");
    exit;
}
