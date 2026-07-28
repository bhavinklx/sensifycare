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
                Edit Health Parameter
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="healthParameterFrm" method="post" action="{{ route("health-parameter-update") }}">
        {{ csrf_field() }}
        <input type="hidden" name="health_parameter_id" id="health_parameter_id" value="{{ $healthParameterDetail->health_parameter_id }}">
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Health Parameter</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row g-3">
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_name">Parameter Name</label>
                                        <input type="text" class="form-control" id="health_parameter_name" name="health_parameter_name" placeholder="Enter Parameter Name" value="{{ $healthParameterDetail->health_parameter_name }}">
                                        <div class="invalid-feedback" id="msg_health_parameter_name"></div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_question">Question</label>
                                        <input type="text" class="form-control" id="health_parameter_question" name="health_parameter_question" placeholder="Enter Question" value="{{ $healthParameterDetail->health_parameter_question }}">
                                        <div class="invalid-feedback" id="msg_health_parameter_question"></div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_show_type">Show Type</label>
                                        <select class="form-select" id="health_parameter_show_type" name="health_parameter_show_type">
                                            <option value="dropdown" {{ $healthParameterDetail->health_parameter_show_type == 'dropdown' ? 'selected' : '' }}>Dropdown</option>
                                            <option value="radio" {{ $healthParameterDetail->health_parameter_show_type == 'radio' ? 'selected' : '' }}>Radio Button</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_health_parameter_show_type"></div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="health_parameter_status">Status</label>
                                        <select class="form-select" id="health_parameter_status" name="health_parameter_status">
                                            <option value="1" {{ $healthParameterDetail->health_parameter_status == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $healthParameterDetail->health_parameter_status == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_health_parameter_status"></div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Options (Max 4)</label>
                                        <div id="options_container">
                                            @php
                                                $options = [
                                                    $healthParameterDetail->health_parameter_option1,
                                                    $healthParameterDetail->health_parameter_option2,
                                                    $healthParameterDetail->health_parameter_option3,
                                                    $healthParameterDetail->health_parameter_option4,
                                                ];
                                            @endphp
                                            @for ($i = 0; $i < 4; $i++)
                                                <div class="input-group mb-2 option-row">
                                                    <input type="text" class="form-control" name="health_parameter_option{{ $i + 1 }}" placeholder="Option {{ $i + 1 }}" value="{{ $options[$i] ?? '' }}">
                                                </div>
                                            @endfor
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
                                            Update Health Parameter
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
