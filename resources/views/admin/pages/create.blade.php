@extends('admin.layouts.app')
@section('content')
    <!-- App hero header starts -->
    <div class="app-hero-header d-flex align-items-center">
        <!-- Breadcrumb starts -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ri-home-8-line lh-1 pe-3 me-3 border-end"></i>
                <a href="{{ route('dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Add Page
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="pagesFrm" method="post" action="{{ route('pages-insert') }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Add Page</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row gx-3">
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="roles">Page Parent</label>
                                        <select class="form-select" id="page_parent" name="page_parent">
                                            <option value="0">Select as Parent</option>
                                            @foreach ($parentPages as $pages)
                                                <option value="{{ $pages->page_id }}">{{ $pages->page_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_link">Page Link</label>
                                        <input type="text" class="form-control" id="page_link" name="page_link"
                                            placeholder="Enter Page Link">
                                        <div class="invalid-feedback" id="msg_page_link"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Page Title</label>
                                        <input type="text" class="form-control" id="page_title" name="page_title"
                                            placeholder="Enter Page Title">
                                        <div class="invalid-feedback" id="msg_page_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="phone">Page Slug</label>
                                        <input type="text" class="form-control" id="page_slug" name="page_slug"
                                            placeholder="Enter Page Slug">
                                        <div class="invalid-feedback" id="msg_page_slug"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Page Image -->
                                <div class="col-lg-6">
                                    <form class="dropzone" id="image-upload" method="POST" action="{{ route('pages-image-upload') }}" enctype="multipart/form-data">
                                        @csrf
                                        <label class="form-label">Page Image</label>
                                        <div class="dropzone dz-clickable" id="image-upload">
                                            <div class="dz-message">
                                                <button type="button" class="dz-button">
                                                    Click here to upload your photo
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <input type="hidden" name="page_image" id="page_image">

                                </div>

                                <!-- Page Fields -->
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="page_meta_title">Meta Title</label>
                                                <input type="text" class="form-control" id="page_meta_title"
                                                    name="page_meta_title" placeholder="Enter Meta Title">
                                                <div class="invalid-feedback" id="msg_page_meta_title"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="page_title">Meta Keyword</label>
                                                <input type="text" class="form-control" id="page_meta_keyword"
                                                    name="page_meta_keyword" placeholder="Meta Keyword">
                                                <div class="invalid-feedback" id="msg_page_title"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-6 col-sm-6 mt-sm-2">
                                            <div class="mb-3">
                                                <label class="form-label" for="page_link">Meta Description</label>
                                                <textarea type="text" class="form-control" id="page_meta_desc" name="page_meta_desc" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mb-3">
                                <label class="form-label">Description</label>
                                <div id="fullEditor">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_status">Status</label>
                                        <select class="form-select" id="page_status" name="page_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_header_status">Header Status</label>
                                        <select class="form-select" id="page_header_status" name="page_header_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_footer_status">Footer Status</label>
                                        <select class="form-select" id="page_footer_status" name="page_footer_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_app_status">App Status</label>
                                        <select class="form-select" id="page_app_status" name="page_app_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('pages-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Add Page
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
        $('#page_title').keyup(function(e) {
            $.ajax({
                url: "{{ route('pages-create-slug') }}",
                type: "GET",
                data: {
                    'page_title': $(this).val()
                },
                success: function(response) {
                    $('#p_slug').addClass('focused')
                    $('#page_slug').val(response.slug);
                }
            });
        });

        $('#pagesFrm').submit(function(e) {
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

        // Dropzone image upload
        Dropzone.autoDiscover = false;
        let dz = new Dropzone("#image-upload", {
            url: "{{ route('pages-image-upload') }}",
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png,.webp",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        dz.on("success", function(file, response) {
            document.getElementById('page_image').value = response.filename;
        });
    </script>
@endsection