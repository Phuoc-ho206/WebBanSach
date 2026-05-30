<?php
/** @var string $pageTitle Tiêu đề trang — set trước khi include */
$pageTitle = $pageTitle ?? 'WebBanSach';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">

    <!-- Layout -->
    <link rel="stylesheet" href="/WebBanSach/assets/css/layout/navbar.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/layout/footer.css">

    <!-- Primitives -->
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/button.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/form.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/card.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/badge.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/alert-toast.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/modal.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/pagination.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/table.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/primitives/spinner.css">
</head>

<body>