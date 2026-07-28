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
            <li class="breadcrumb-item">
                <a href="{{ route("health-parameter-list") }}">Health Parameter List</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Add Health Parameter
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="healthParameterFrm" method="post" action="{{ route("health-parameter-insert") }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Add Health Parameter</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row g-3">
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_name">Parameter Name</label>
                                        <input type="text" class="form-control" id="health_parameter_name" name="health_parameter_name" placeholder="Enter Parameter Name">
                                        <div class="invalid-feedback" id="msg_health_parameter_name"></div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_question">Question</label>
                                        <input type="text" class="form-control" id="health_parameter_question" name="health_parameter_question" placeholder="Enter Question">
                                        <div class="invalid-feedback" id="msg_health_parameter_question"></div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_show_type">Show Type</label>
                                        <select class="form-select" id="health_parameter_show_type" name="health_parameter_show_type">
                                            <option value="dropdown">Dropdown</option>
                                            <option value="radio">Radio Button</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_health_parameter_show_type"></div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_status">Status</label>
                                        <select class="form-select" id="health_parameter_status" name="health_parameter_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_health_parameter_status"></div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Options (Max 4)</label>
                                        <div id="options_container">
                                            <div class="input-group mb-2 option-row">
                                                <input type="text" class="form-control" name="health_parameter_option1" placeholder="Option 1">
                                                <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">Remove</button>
                                            </div>
                                            <div class="input-group mb-2 option-row">
                                                <input type="text" class="form-control" name="health_parameter_option2" placeholder="Option 2">
                                                <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">Remove</button>
                                            </div>
                                            <div class="input-group mb-2 option-row">
                                                <input type="text" class="form-control" name="health_parameter_option3" placeholder="Option 3">
                                                <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">Remove</button>
                                            </div>
                                            <div class="input-group mb-2 option-row">
                                                <input type="text" class="form-control" name="health_parameter_option4" placeholder="Option 4">
                                                <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">Remove</button>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback d-block" id="msg_health_parameter_option1"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Row ends -->

                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('health-parameter-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Add Health Parameter
                                        </button>
                                    </div>
                                </div>
                            </div>
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
        $('#healthParameterFrm').submit(function(e) {
            e.preventDefault();

            $('#loading-wrapper').fadeIn(200);
            let formData = new FormData(this);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                enctype: 'multipart/form-data',
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
