@php
    $fileName = request()->route()->getName();
@endphp

<!-- Sidebar wrapper starts -->
<nav id="sidebar" class="sidebar-wrapper">
    <!-- Sidebar profile starts -->
    <div class="sidebar-profile">
        <img src="{{ url('assets/images/logo.png') }}" class="img-shadow img-3x me-3 rounded-5" alt="Sensify Care">
        <div class="m-0">
            <h5 class="mb-1 profile-name text-nowrap text-truncate">{{ Auth::user()->name }}</h5>
            <p class="m-0 small profile-name text-nowrap text-truncate">{{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</p>
        </div>
    </div>
    <!-- Sidebar profile ends -->

    <!-- Sidebar menu starts -->
    <div class="sidebarMenuScroll">
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route("dashboard") }}">
                    <i class="ri-home-6-line"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            @if(auth()->user()->hasPermissionTo('user-list', 'web') || auth()->user()->hasPermissionTo('role-list', 'web'))
                <li class="treeview {{ ($fileName == 'user-list' || $fileName == 'user-add' || $fileName == 'user-edit' || $fileName == 'role-list' || $fileName == 'role-add' || $fileName == 'role-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-nurse-line"></i>
                        <span class="menu-text">Administrators</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('user-list')
                            <li>
                                <a href="{{ route("user-list") }}" class="{{ ($fileName == 'user-list') ? 'active-sub' : '' }}">Administrator List</a>
                            </li>
                        @endcan
                        @can('user-add')
                            <li>
                                <a href="{{ route("user-add") }}" class="{{ ($fileName == 'user-add' || $fileName == 'user-edit') ? 'active-sub' : '' }}">Add Administrator</a>
                            </li>
                        @endcan
                        @can('role-list')
                            <li>
                                <a href="{{ route("role-list") }}" class="{{ ($fileName == 'role-list') ? 'active-sub' : '' }}">Role List</a>
                            </li>
                        @endcan
                        @can('role-add')
                            <li>
                                <a href="{{ route("role-add") }}" class="{{ ($fileName == 'role-add' || $fileName == 'role-edit') ? 'active-sub' : '' }}">Add Role</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('patient-list') || auth()->user()->can('patient-add'))
                <li class="treeview {{ ($fileName == 'patient-list' || $fileName == 'patient-add' || $fileName == 'patient-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-heart-pulse-line"></i>
                        <span class="menu-text">Patients</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('patient-list')
                            <li>
                                <a href="{{ route("patient-list") }}" class="{{ ($fileName == 'patient-list') ? 'active-sub' : '' }}">Patient List</a>
                            </li>
                        @endcan
                        {{--@can('patient-add')
                            <li>
                                <a href="{{ route("patient-add") }}" class="{{ ($fileName == 'patient-add' || $fileName == 'patient-edit') ? 'active-sub' : '' }}">Add Patient</a>
                            </li>
                        @endcan--}}
                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('doctor-list') || auth()->user()->can('doctor-add'))
                <li class="treeview {{ ($fileName == 'doctor-list' || $fileName == 'doctor-add' || $fileName == 'doctor-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-stethoscope-line"></i>
                        <span class="menu-text">Doctors</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('doctor-list')
                            <li>
                                <a href="{{ route("doctor-list") }}" class="{{ ($fileName == 'doctor-list') ? 'active-sub' : '' }}">Doctor List</a>
                            </li>
                        @endcan
                        {{--@can('doctor-add')
                            <li>
                                <a href="{{ route("doctor-add") }}" class="{{ ($fileName == 'doctor-add' || $fileName == 'doctor-edit') ? 'active-sub' : '' }}">Add Doctor</a>
                            </li>
                        @endcan--}}
                    </ul>
            @endif

            @if(auth()->user()->can('symptom-list') || auth()->user()->can('symptom-add'))
                <li class="treeview {{ ($fileName == 'symptom-list' || $fileName == 'symptom-add' || $fileName == 'symptom-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-health-book-line"></i>
                        <span class="menu-text">Symptoms</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('symptom-list')
                            <li>
                                <a href="{{ route("symptom-list") }}" class="{{ ($fileName == 'symptom-list') ? 'active-sub' : '' }}">Symptom List</a>
                            </li>
                        @endcan
                        @can('symptom-add')
                            <li>
                                <a href="{{ route("symptom-add") }}" class="{{ ($fileName == 'symptom-add' || $fileName == 'symptom-edit') ? 'active-sub' : '' }}">Add Symptom</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('banner-list') || auth()->user()->can('banner-add'))
                <li class="treeview {{ ($fileName == 'banner-list' || $fileName == 'banner-add' || $fileName == 'banner-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-file-image-line"></i>
                        <span class="menu-text">Banners</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('banner-list')
                            <li>
                                <a href="{{ route("banner-list") }}" class="{{ ($fileName == 'banner-list') ? 'active-sub' : '' }}">Banner List</a>
                            </li>
                        @endcan
                        @can('banner-add')
                            <li>
                                <a href="{{ route("banner-add") }}" class="{{ ($fileName == 'banner-add' || $fileName == 'banner-edit') ? 'active-sub' : '' }}">Add Banner</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('pages-list') || auth()->user()->can('pages-add'))
                <li class="treeview {{ ($fileName == 'pages-list' || $fileName == 'pages-add' || $fileName == 'pages-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-pantone-line"></i>
                        <span class="menu-text">Pages</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('pages-list')
                            <li>
                                <a href="{{ route("pages-list") }}" class="{{ ($fileName == 'pages-list') ? 'active-sub' : '' }}">Page List</a>
                            </li>
                        @endcan
                        @can('pages-add')
                            <li>
                                <a href="{{ route("pages-add") }}" class="{{ ($fileName == 'pages-add' || $fileName == 'pages-edit') ? 'active-sub' : '' }}">Add Page</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('bcategory-list', 'web') || auth()->user()->can('blog-list', 'web'))
                <li class="treeview {{ ($fileName == 'bcategory-list' || $fileName == 'bcategory-add' || $fileName == 'bcategory-edit' || $fileName == 'blog-list' || $fileName == 'blog-add' || $fileName == 'blog-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-news-line"></i>
                        <span class="menu-text">Blogs</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('bcategory-list')
                            <li>
                                <a href="{{ route("bcategory-list") }}" class="{{ ($fileName == 'bcategory-list') ? 'active-sub' : '' }}">Category List</a>
                            </li>
                        @endcan
                        @can('bcategory-add')
                            <li>
                                <a href="{{ route("bcategory-add") }}" class="{{ ($fileName == 'bcategory-add' || $fileName == 'bcategory-edit') ? 'active-sub' : '' }}">Add Category</a>
                            </li>
                        @endcan
                        @can('blog-list')
                            <li>
                                <a href="{{ route("blog-list") }}" class="{{ ($fileName == 'blog-list') ? 'active-sub' : '' }}">Blog List</a>
                            </li>
                        @endcan
                        @can('blog-add')
                            <li>
                                <a href="{{ route("blog-add") }}" class="{{ ($fileName == 'blog-add' || $fileName == 'blog-edit') ? 'active-sub' : '' }}">Add Blog</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            <li class="{{ ($fileName == 'ai-assistant') ? 'active current-page' : '' }}">
                <a href="{{ route("ai-assistant") }}">
                    <i class="ri-robot-2-line"></i>
                    <span class="menu-text">AI Assistant</span>
                </a>
            </li>

            @if(auth()->user()->can('setting-edit'))
                <li class="{{ ($fileName == 'setting') ? 'active current-page' : '' }}">
                    <a href="{{ route("setting") }}">
                        <i class="ri-settings-5-line"></i>
                        <span class="menu-text">Settings</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    <!-- Sidebar menu ends -->

    <!-- Sidebar contact starts -->
    <div class="sidebar-contact">
        <p class="fw-light mb-1 text-nowrap text-truncate">Emergency Contact</p>
        <h5 class="m-0 lh-1 text-nowrap text-truncate">09426424556</h5>
        <i class="ri-phone-line"></i>
    </div>
    <!-- Sidebar contact ends -->
</nav>
<!-- Sidebar wrapper ends -->