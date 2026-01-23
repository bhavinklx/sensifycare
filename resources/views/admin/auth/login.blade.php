<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap Gallery - Medical Admin Templates & Dashboards</title>

    <!-- Meta -->
    <meta name="description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:title" content="Admin Templates - Dashboard Templates">
    <meta property="og:description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:type" content="Website">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}">

    <link rel="stylesheet" href="{{ asset('assets/fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
</head>

<body class="login-bg">
    <!-- Container starts -->
    <div class="container">
        <!-- Auth wrapper starts -->
        <div class="auth-wrapper">
            <!-- Form starts -->
            <form id="loginFrm" role="form" method="post" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="auth-box">
                    {{--<a href="index.html" class="auth-logo mb-4">
                        <img src="{{ asset('assets/images/logo-dark.svg') }}" alt="Bootstrap Gallery">
                    </a>--}}
                    <h4 class="mb-4">Login</h4>
                    <div class="mb-3">
                        <label class="form-label" for="email">Your email <span class="text-danger">*</span></label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="Enter your email">
                        @error("email")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password">Your password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="new-password" placeholder="Enter password">
                        @error("password")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{--<div class="d-flex justify-content-end mb-3">
                        <a href="forgot-password.html" class="text-decoration-underline">Forgot password?</a>
                    </div>--}}
                    <div class="mb-3 d-grid gap-2">
                        <button name="submit" type="submit" class="btn btn-primary">Login</button>
                        {{--<a href="signup.html" class="btn btn-secondary">Not registered? Signup</a>--}}
                    </div>
                </div>
            </form>
            <!-- Form ends -->
        </div>
        <!-- Auth wrapper ends -->
    </div>
    <!-- Container ends -->

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <!--Custom JavaScript -->
    <script type="text/javascript">
        if ($("#loginFrm").length > 0) {
            $("#loginFrm").validate({
                errorElement: "div",
                errorClass: "invalid-feedback",
                highlight: function (element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    email: {
                        required: true,
                        maxlength: 50,
                        email: true,
                    },
                    password: {
                        required: true,
                    },
                },

                messages: {
                    email: {
                        required: "Please enter your email",
                        email: "Please enter your valid email",
                        maxlength: "The email name should less than or equal to 50 characters",
                    },
                    password: {
                        required: "Please enter your password",
                    },
                },
            });
        }
    </script>
</body>
<!-- END BODY -->
</html>