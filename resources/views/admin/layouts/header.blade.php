<!-- App header starts -->
<div class="app-header d-flex align-items-center">
    <!-- Toggle buttons starts -->
    <div class="d-flex">
        <button class="toggle-sidebar">
            <i class="ri-menu-line"></i>
        </button>
        <button class="pin-sidebar">
            <i class="ri-menu-line"></i>
        </button>
    </div>
    <!-- Toggle buttons ends -->

    <!-- App brand starts -->
    {{--<div class="app-brand ms-3">
        <a href="index.html" class="d-lg-block d-none">
            <img src="{{ url('assets/images/logo.svg') }}" class="logo" alt="Medicare Admin Template">
        </a>
        <a href="index.html" class="d-lg-none d-md-block">
            <img src="{{ url('assets/images/logo-sm.svg') }}" class="logo" alt="Medicare Admin Template">
        </a>
    </div>--}}
    <!-- App brand ends -->

    <!-- App header actions starts -->
    <div class="header-actions">
        <!-- AI Chat start -->
        <div class="me-2">
            <button class="btn btn-outline-primary btn-sm rounded-pill d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#aiChatModal">
                <i class="ri-robot-2-line me-1"></i> AI Assistant
            </button>
        </div>
        <!-- AI Chat end -->
        <!-- Header user settings starts -->
        <div class="dropdown ms-2">
            <a id="userSettings" class="dropdown-toggle d-flex align-items-center" href="#!" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-box">
                    {{ collect(explode(' ', Auth::user()->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') }}
                    <span class="status online"></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg">
                <div class="px-3 py-2">
                    <span class="small">{{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</span>
                    <h6 class="m-0">{{ Auth::user()->name }}</h6>
                </div>
                <div class="mx-3 my-2 d-grid">
                    <a href="{{ url("/") }}" target="_blank" class="btn btn-primary mb-1">View Website</a>
                    <a href="{{ route("logout") }}" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
        <!-- Header user settings ends -->
    </div>
    <!-- App header actions ends -->

</div>
<!-- App header ends -->