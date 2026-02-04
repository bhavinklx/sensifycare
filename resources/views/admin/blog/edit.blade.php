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
                Edit Blog
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="blogFrm" method="post" action="{{ route('blog-update') }}">
        <input type="hidden" name="blog_id" value="{{ $blogDetail->blog_id }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Blog</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row gx-3">
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="roles">Category</label>
                                        <select class="form-select" id="bcategory_id" name="bcategory_id">
                                            <option value="0">Select as Parent</option>
                                            @foreach($bcategoryDetail as $bcategory)
                                                <option value="{{ $bcategory->bcategory_id }}" {{ ($bcategory->bcategory_id == $blogDetail->bcategory_id) ? 'selected="selected"' : '' }}>{{ $bcategory->bcategory_title }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="msg_bcategory_id"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_date">Blog Date</label>
                                        <input type="text" class="form-control" id="blog_date" name="blog_date" placeholder="Select Blog Date" value="{{ date('d-m-Y', strtotime($blogDetail->blog_date)) }}">
                                        <div class="invalid-feedback" id="msg_blog_date"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_title">Blog Title</label>
                                        <input type="text" class="form-control" id="blog_title" name="blog_title" placeholder="Enter Blog Title" value="{{ $blogDetail->blog_title }}">
                                        <div class="invalid-feedback" id="msg_blog_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_slug">Blog Slug</label>
                                        <input type="text" class="form-control" id="blog_slug" name="blog_slug" placeholder="Enter Blog Slug" value="{{ $blogDetail->blog_slug }}">
                                        <div class="invalid-feedback" id="msg_blog_slug"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Page Image -->
                                <div class="col-lg-6">
                                    <form class="dropzone" id="image-upload" method="POST" action="{{ route('blog-image-upload') }}" enctype="multipart/form-data">
                                        @csrf
                                        <label class="form-label">Blog Image</label>
                                        <div class="dropzone dz-clickable" id="image-upload">
                                            <div class="dz-message">
                                                <button type="button" class="dz-button">
                                                    Click here to upload your photo
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <input type="hidden" name="blog_image" id="blog_image">
                                </div>

                                <!-- Page Fields -->
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="blog_meta_title">Meta Title</label>
                                                <input type="text" class="form-control" id="blog_meta_title" name="blog_meta_title" placeholder="Enter Meta Title" value="{{ $blogDetail->blog_meta_title }}">
                                                <div class="invalid-feedback" id="msg_blog_meta_title"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="blog_meta_keyword">Meta Keyword</label>
                                                <input type="text" class="form-control" id="blog_meta_keyword" name="blog_meta_keyword" placeholder="Meta Keyword" value="{{ $blogDetail->blog_meta_keyword }}">
                                                <div class="invalid-feedback" id="msg_blog_meta_keyword"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-6 col-sm-6 mt-sm-2">
                                            <div class="mb-3">
                                                <label class="form-label" for="blog_meta_desc">Meta Description</label>
                                                <textarea type="text" class="form-control" id="blog_meta_desc" name="blog_meta_desc" rows="2">{{ $blogDetail->blog_meta_desc }}</textarea>
                                                <div class="invalid-feedback" id="msg_blog_meta_desc"></div>
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
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('blog-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Update Blog
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
        $('#blog_title').keyup(function(e) {
            $.ajax({
                url: "{{ route('blog-create-slug') }}",
                type: "GET",
                data: {
                    'blog_title': $(this).val()
                },
                success: function(response) {
                    $('#blog_slug').val(response.slug);
                }
            });
        });

        $('#blogFrm').submit(function(e) {
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

        Dropzone.autoDiscover = false;
        let existingImage = "{{ $blogDetail->blog_image ?? '' }}";
        let dz = new Dropzone("#image-upload", {
            url: "{{ route('blog-image-upload') }}",
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png,.webp",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function() {
                let myDropzone = this;

                //PRELOAD EXISTING IMAGE
                if (existingImage) {
                    let mockFile = {
                        name: existingImage,
                        size: 12345,
                        accepted: true
                    };

                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.emit(
                            "thumbnail",
                            mockFile,
                            "{{ asset('uploads/blog') }}/" + existingImage
                    );
                    myDropzone.emit("complete", mockFile);

                    myDropzone.files.push(mockFile);
                    document.getElementById('blog_image').value = existingImage;
                }

                // Upload new image
                myDropzone.on("success", function(file, response) {
                    document.getElementById('blog_image').value = response.filename;
                });

                // Remove image
                myDropzone.on("removedfile", function() {
                    document.getElementById('blog_image').value = "";
                });
            }
        });
    </script>
@endsection