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
                                        <input type="text" class="form-control" id="page_link" name="page_link" placeholder="Enter Page Link">
                                        <div class="invalid-feedback" id="msg_page_link"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_title">Page Title</label>
                                        <input type="text" class="form-control" id="page_title" name="page_title" placeholder="Enter Page Title">
                                        <div class="invalid-feedback" id="msg_page_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="page_slug">Page Slug</label>
                                        <input type="text" class="form-control" id="page_slug" name="page_slug" placeholder="Enter Page Slug">
                                        <div class="invalid-feedback" id="msg_page_slug"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Page Image -->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="image-upload">Page Image</label>
                                        <div class="dropzone" id="image-upload">
                                            <div class="dz-message">
                                                <button type="button" class="dz-button">
                                                    Click here to upload your photo
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="page_image" id="page_image" value="">
                                        <div class="invalid-feedback" id="msg_page_image"></div>
                                    </div>
                                </div>

                                <!-- Page Fields -->
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="page_meta_title">Meta Title</label>
                                                <input type="text" class="form-control" id="page_meta_title" name="page_meta_title" placeholder="Enter Meta Title">
                                                <div class="invalid-feedback" id="msg_page_meta_title"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="page_meta_keyword">Meta Keyword</label>
                                                <input type="text" class="form-control" id="page_meta_keyword" name="page_meta_keyword" placeholder="Meta Keyword">
                                                <div class="invalid-feedback" id="msg_page_meta_keyword"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-6 col-sm-6 mt-sm-2">
                                            <div class="mb-3">
                                                <label class="form-label" for="page_meta_desc">Meta Description</label>
                                                <textarea type="text" class="form-control" id="page_meta_desc" name="page_meta_desc" rows="2"></textarea>
                                                <div class="invalid-feedback" id="msg_page_meta_desc"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mb-3">
                                <label for="page_desc" class="form-label">Description</label>
                                <textarea id="page_desc" name="page_desc"></textarea>
                                <script type="text/javascript">
                                    CKEDITOR.replace( 'page_desc',
                                        {
                                            filebrowserBrowseUrl : '{{ url('assets/ckfinder/ckfinder.html') }}',
                                            filebrowserImageBrowseUrl : '{{ url('assets/ckfinder/ckfinder.html?Type=Images') }}',
                                            filebrowserFlashBrowseUrl : '{{ url('assets/ckfinder/ckfinder.html?Type=Flash') }}',
                                            filebrowserUploadUrl : '{{ route('ckeditor-upload', ['_token' => csrf_token()]) }}',
                                            filebrowserImageUploadUrl : '{{ route('ckeditor-upload', ['_token' => csrf_token()]) }}',
                                            filebrowserFlashUploadUrl : '{{ route('ckeditor-upload', ['_token' => csrf_token()]) }}',
                                            enterMode: CKEDITOR.ENTER_P,
                                        }
                                    );
                                </script>
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
                    $('#page_slug').val(response.slug);
                }
            });
        });

        $('#pagesFrm').submit(function(e) {
            e.preventDefault();
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
                CKEDITOR.config.allowedContent = true;
            }

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
        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(function(dz) {
                if (dz.element && dz.element.id === 'image-upload') {
                    dz.destroy();
                }
            });
        }

        let dz = new Dropzone("#image-upload", {
            url: "{{ route('pages-image-upload') }}",
            paramName: "file",
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png,.webp",
            addRemoveLinks: true,
            dictDefaultMessage: "Click here to upload your photo",
            dictRemoveFile: "Remove file",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function() {
                let myDropzone = this;

                this.on("maxfilesexceeded", function(file) {
                    myDropzone.removeAllFiles();
                    myDropzone.addFile(file);
                });

                this.on("success", function(file, response) {
                    file.serverFileName = response.filename;
                    $('#page_image').val(response.filename);
                    $('#msg_page_image').html('');
                    $('#page_image').removeClass('is-invalid');
                });

                this.on("removedfile", function(file) {
                    let removedName = file.serverFileName || $('#page_image').val();
                    $('#page_image').val('');

                    if (removedName) {
                        $.ajax({
                            url: "{{ route('pages-image-delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                filename: removedName
                            }
                        });
                    }
                });

                this.on("error", function(file, errorMessage) {
                    let msg = (typeof errorMessage === 'object' && errorMessage.message) ? errorMessage.message : errorMessage;
                    if (typeof errorMessage === 'object' && errorMessage.errors && errorMessage.errors.file) {
                        msg = errorMessage.errors.file[0];
                    }
                    $('#msg_page_image').html(msg).show();
                });
            }
        });
    </script>
@endsection