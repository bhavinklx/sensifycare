@extends("admin.layouts.app")
@section('content')
    <!-- App hero header starts -->
    <div class="app-hero-header d-flex align-items-center">
        <!-- Breadcrumb starts -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ri-home-8-line lh-1 pe-3 me-3 border-end"></i>
                <a href="{{ route("dashboard") }}">Home</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Edit Role
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->
    <!-- App body starts -->
    <form id="roleFrm" method="post" action="{{ route('role-update') }}">
        <input type="hidden" name="role_id" value="{{ $role->id }}" >
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Role</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="name">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Role Name" value="{{ $role->name }}">
                                <div class="invalid-feedback" id="msg_name"></div>
                            </div>

                            <!-- Row starts -->
                            <div class="row gx-3">
                                <label class="form-label" for="name">Permission</label>
                                <div class="row ps-lg-4">
                                    <div class="col-lg-2 form-check ps-lg-4" style="margin: 3px 0 0 0; display: inline-block;">
                                        <input class="form-check-input" type="checkbox" id="checkall" name="checkall">
                                        <label class="form-check-label" for="checkall">Check All</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr class="my-2">
                                </div>
                                <div class="row ps-lg-4">
                                    @if ($permission && count($permission) > 0)
                                        @foreach ($permission as $key => $value)
                                            <div class="col-lg-2 form-check" style="margin: 3px 0 0 0; display: inline-block;">
                                                <input class="form-check-input check_class" type="checkbox" id="permission{{ $value->id }}" name="permission[]" value="{{ $value->name }}" {{ in_array($value->id, $rolePermissions) ? 'checked="checked"' : '' }}>
                                                <label class="form-check-label" for="permission{{ $value->id }}">{{ $value->name }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="invalid-feedback ps-lg-0" id="msg_permission"></div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route("role-list") }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Update Role
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Row ends -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->
        </div>
    </form>
    <!-- App body ends -->
@endsection

@section('page-js')
    <script type="text/javascript">
        $('#roleFrm').submit(function(e) {
            e.preventDefault();

            $('#loading-wrapper').fadeIn(200);
            let formData = new FormData(this);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#loading-wrapper').fadeOut(200);
                    window.location.href = res.redirect_url;
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#' + key).addClass('is-invalid');
                            $('#msg_' + key).html(val[0]).css('display', 'block');
                        });
                    }
                    $('#loading-wrapper').fadeOut(200);
                }
            });
        });
    </script>
@endsection
