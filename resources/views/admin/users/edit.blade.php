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
                Edit Administrator
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <!-- Start Page Content -->
    <form id="userFrm" method="post" action="{{ route("user-update") }}">
        <input type="hidden" id="user_id" name="user_id" value="{{ $userDetail->id }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Administrator</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row gx-3">
                                <div class="col-xxl-3 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="name">Administrator Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $userDetail->name }}" placeholder="Enter Administrator Name">
                                        <div class="invalid-feedback" id="msg_name"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Administrator Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $userDetail->email }}" placeholder="Enter email address">
                                        <div class="invalid-feedback" id="msg_email"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="phone">Administrator Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $userDetail->phone }}" placeholder="Enter phone number" maxlength="10">
                                        <div class="invalid-feedback" id="msg_phone"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="roles">Role Type</label>
                                        <select class="form-select" id="roles" name="roles[]">
                                            <option value="0" >Select as Role</option>
                                            @if (is_array($roleDetail) && count($roleDetail) > 0)
                                                @foreach ($roleDetail as $role)
                                                    <option value="{{ $role }}" {{ (@in_array($role, $userRole)) ? 'selected="selected"' : '' }}>{{ $role }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route("user-list") }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Update Administrator
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
        $('#userFrm').submit(function(e) {
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
                            $('#msg_' + key).html(val[0]);
                        });
                    }
                    $('#loading-wrapper').fadeOut(200);
                }
            });
        });
    </script>
@endsection
