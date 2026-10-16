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
                <a href="{{ route("fcategory-list") }}">FAQ Categories</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Edit FAQ Category
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="fcategoryFrm" method="post" action="{{ route("fcategory-update") }}">
        <input type="hidden" name="fcategory_id" value="{{ $fcategoryDetail->fcategory_id }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit FAQ Category</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row gx-3">
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="fcategory_title">Category Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fcategory_title" name="fcategory_title" placeholder="Enter Category Title" value="{{ $fcategoryDetail->fcategory_title }}">
                                        <div class="invalid-feedback" id="msg_fcategory_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="fcategory_slug">Category Slug <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fcategory_slug" name="fcategory_slug" placeholder="Enter Category Slug" value="{{ $fcategoryDetail->fcategory_slug }}">
                                        <div class="invalid-feedback" id="msg_fcategory_slug"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="fcategory_status">Status</label>
                                        <select class="form-select" id="fcategory_status" name="fcategory_status">
                                            <option value="1" {{ ($fcategoryDetail->fcategory_status == '1') ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ($fcategoryDetail->fcategory_status == '0') ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_fcategory_status"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('fcategory-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Update Category
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
        $('#fcategory_title').keyup(function(e) {
            $.ajax({
                url: "{{ route('fcategory-create-slug') }}",
                type: "GET",
                data: {
                    'fcategory_title': $(this).val()
                },
                success: function(response) {
                    $('#fcategory_slug').val(response.slug);
                }
            });
        });

        $('#fcategoryFrm').submit(function(e) {
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
