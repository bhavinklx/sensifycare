<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel | Sensify Care</title>

    <!-- Meta -->
    <meta name="description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:title" content="Admin Templates - Dashboard Templates">
    <meta property="og:description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:type" content="Website">
    <link rel="shortcut icon" href="assets/images/favicon.svg">

    <!-- This page CSS -->
    @include('admin.layouts.styles')
</head>

<body>
    <!-- Loading starts -->
    <div id="loading-wrapper" style="display: none !important;">
        <div class='spin-wrapper'>
            <div class='spin'>
                <div class='inner'></div>
            </div>
            <div class='spin'>
                <div class='inner'></div>
            </div>
            <div class='spin'>
                <div class='inner'></div>
            </div>
            <div class='spin'>
                <div class='inner'></div>
            </div>
            <div class='spin'>
                <div class='inner'></div>
            </div>
            <div class='spin'>
                <div class='inner'></div>
            </div>
        </div>
    </div>
    <!-- Loading ends -->

    <!-- Page wrapper starts -->
    <div class="page-wrapper">
        @include('admin.layouts.header')
        <!-- Main container starts -->
        <div class="main-container">
            @include('admin.layouts.left')
            <!-- App container starts -->
            <div class="app-container">
                @yield('content')
                @include('admin.layouts.footer')
            </div>
            <!-- App container ends -->
        </div>
        <!-- Main container ends -->
    </div>
    <!-- Page wrapper ends -->

    <!-- All Jquery -->
    @include('admin.chat.chat')
    @include('admin.layouts.scripts')
    @yield('page-js')
</body>
</html>
