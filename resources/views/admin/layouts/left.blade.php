@php
    $fileName = request()->route()->getName();
@endphp

<!-- Sidebar wrapper starts -->
<nav id="sidebar" class="sidebar-wrapper">
    <!-- Sidebar profile starts -->
    <div class="sidebar-profile">
        <img src="{{ url('assets/images/user6.png') }}" class="img-shadow img-3x me-3 rounded-5" alt="Hospital Admin Templates">
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

            @if(auth()->user()->can('pages-list') || auth()->user()->can('pages-add'))
                <li class="treeview {{ ($fileName == 'pages-list' || $fileName == 'pages-add' || $fileName == 'pages-edit') ? 'active current-page' : '' }}">
                    <a href="javascript: void (0)">
                        <i class="ri-nurse-line"></i>
                        <span class="menu-text">Pages</span>
                    </a>
                    <ul class="treeview-menu">
                        @can('role-list')
                            <li>
                                <a href="{{ route("pages-list") }}" class="{{ ($fileName == 'pages-list') ? 'active-sub' : '' }}">Page List</a>
                            </li>
                        @endcan
                        @can('role-add')
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
                        <i class="ri-nurse-line"></i>
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