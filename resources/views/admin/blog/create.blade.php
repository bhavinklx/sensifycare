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
                Add Blog
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="blogFrm" method="post" action="{{ route('blog-insert') }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Add Blog</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row gx-3">
                                <div class="col-xxl-3 col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="bcategory_id">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" id="bcategory_id" name="bcategory_id">
                                            <option value="">Select Category</option>
                                            @foreach($bcategoryDetail as $bcategory)
                                                <option value="{{ $bcategory->bcategory_id }}">{{ $bcategory->bcategory_title }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="msg_bcategory_id"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="bsubcategory_id">Subcategory</label>
                                        <select class="form-select" id="bsubcategory_id" name="bsubcategory_id">
                                            <option value="0">Select Subcategory</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_bsubcategory_id"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_date">Blog Date <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="blog_date" name="blog_date" class="form-control datepicker" value="{{ date('d-m-Y') }}" placeholder="DD-MM-YYYY" autocomplete="off">
                                            <span class="input-group-text" style="cursor: pointer;">
                                                <i class="ri-calendar-todo-line"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback" id="msg_blog_date"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_type">Blog Type</label>
                                        <select class="form-select" id="blog_type" name="blog_type">
                                            <option value="image" selected>Image</option>
                                            <option value="video">Video</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_blog_type"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_title">Blog Title</label>
                                        <input type="text" class="form-control" id="blog_title" name="blog_title" placeholder="Enter Blog Title">
                                        <div class="invalid-feedback" id="msg_blog_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_slug">Blog Slug</label>
                                        <input type="text" class="form-control" id="blog_slug" name="blog_slug" placeholder="Enter Blog Slug">
                                        <div class="invalid-feedback" id="msg_blog_slug"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Blog Image Wrapper -->
                                <div class="col-lg-6" id="blog_image_wrapper">
                                    <div class="mb-3">
                                        <label class="form-label" for="image-upload">Blog Image</label>
                                        <div class="dropzone" id="image-upload">
                                            <div class="dz-message">
                                                <button type="button" class="dz-button">
                                                    Click here to upload your photo
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="blog_image" id="blog_image">
                                        <div class="invalid-feedback" id="msg_blog_image"></div>
                                    </div>
                                </div>

                                <!-- Blog Video Wrapper -->
                                <div class="col-lg-6 d-none" id="blog_video_wrapper">
                                    <div class="mb-3">
                                        <label class="form-label" for="blog_video_url">Video URL</label>
                                        <input type="text" class="form-control" id="blog_video_url" name="blog_video_url" placeholder="Enter Video URL">
                                        <div class="invalid-feedback" id="msg_blog_video_url"></div>
                                    </div>
                                </div>

                                <!-- Page Fields -->
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="blog_meta_title">Meta Title</label>
                                                <input type="text" class="form-control" id="blog_meta_title" name="blog_meta_title" placeholder="Enter Meta Title">
                                                <div class="invalid-feedback" id="msg_blog_meta_title"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-6 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="blog_meta_keyword">Meta Keyword</label>
                                                <input type="text" class="form-control" id="blog_meta_keyword" name="blog_meta_keyword" placeholder="Meta Keyword">
                                                <div class="invalid-feedback" id="msg_blog_meta_keyword"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-6 col-sm-6 mt-sm-2">
                                            <div class="mb-3">
                                                <label class="form-label" for="blog_meta_desc">Meta Description</label>
                                                <textarea type="text" class="form-control" id="blog_meta_desc" name="blog_meta_desc" rows="2"></textarea>
                                                <div class="invalid-feedback" id="msg_blog_meta_desc"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mb-3">
                                <label for="blog_desc" class="form-label">Description</label>
                                <textarea id="blog_desc" name="blog_desc"></textarea>
                                <script type="text/javascript">
                                    CKEDITOR.replace( 'blog_desc',
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

                            <hr class="my-4">

                            <!-- FAQ Repeater Container (Two 6-column cards side by side in row) -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="m-0 fw-bold">Frequently Asked Questions (FAQs)</h5>
                            </div>

                            <div id="faq_repeater_container">
                                <div class="row gx-3" id="faq_cards_row">
                                    <!-- Initial FAQ Item #1 (col-6, green Add '+' icon only) -->
                                    <div class="col-xxl-6 col-lg-6 col-sm-12 mb-3 faq-item-col" data-index="0">
                                        <div class="card border h-100 m-0">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-end align-items-center mb-3">
                                                    <button type="button" class="btn btn-success btn-sm add-faq-btn" title="Add FAQ">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Question</label>
                                                    <input type="text" class="form-control faq-question-input" name="faqs[0][question]" placeholder="Enter Question">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Answer</label>
                                                    <textarea class="form-control faq-answer-input" name="faqs[0][answer]" rows="4" placeholder="Enter Answer"></textarea>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('blog-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Add Blog
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

        // Dynamic Subcategories on Category Change
        $('#bcategory_id').change(function() {
            let catId = $(this).val();
            let subSelect = $('#bsubcategory_id');
            subSelect.html('<option value="0">Select Subcategory</option>');
            if (catId && catId != '0') {
                $.ajax({
                    url: "{{ route('blog-get-subcategories') }}",
                    type: "GET",
                    data: { bcategory_id: catId },
                    success: function(data) {
                        if (data && data.length > 0) {
                            $.each(data, function(idx, item) {
                                subSelect.append('<option value="' + item.bcategory_id + '">' + item.bcategory_title + '</option>');
                            });
                        }
                    }
                });
            }
        });

        $('#blogFrm').submit(function(e) {
            e.preventDefault();
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
                CKEDITOR.config.allowedContent=true;
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
            url: "{{ route('blog-image-upload') }}",
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
                    $('#blog_image').val(response.filename);
                    $('#msg_blog_image').html('');
                    $('#blog_image').removeClass('is-invalid');
                });

                this.on("removedfile", function(file) {
                    let removedName = file.serverFileName || $('#blog_image').val();
                    $('#blog_image').val('');

                    if (removedName) {
                        $.ajax({
                            url: "{{ route('blog-image-delete') }}",
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
                    $('#msg_blog_image').html(msg).show();
                });
            }
        });

        function toggleBlogType() {
            var selectedType = $('#blog_type').val();
            if (selectedType === 'video') {
                $('#blog_image_wrapper').addClass('d-none');
                $('#blog_video_wrapper').removeClass('d-none');
            } else {
                $('#blog_image_wrapper').removeClass('d-none');
                $('#blog_video_wrapper').addClass('d-none');
            }
        }

        $('#blog_type').on('change', function() {
            toggleBlogType();
        });
        toggleBlogType();

        // Blog Datepicker initialization based on theme
        if ($.fn.daterangepicker) {
            let initialDate = $('#blog_date').val();
            $('#blog_date').daterangepicker({
                singleDatePicker: true,
                startDate: initialDate ? moment(initialDate, 'DD-MM-YYYY') : moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'DD-MM-YYYY',
                },
            });

            $('.input-group:has(#blog_date) .input-group-text').on('click', function() {
                let picker = $('#blog_date').data('daterangepicker');
                if (picker) {
                    picker.show();
                } else {
                    $('#blog_date').trigger('focus');
                }
            });
        }

        // FAQ Repeater logic
        let faqIndex = 0;

        $(document).on('click', '.add-faq-btn', function() {
            faqIndex++;
            let html = `
                <div class="col-xxl-6 col-lg-6 col-sm-12 mb-3 faq-item-col" data-index="${faqIndex}">
                    <div class="card border h-100 m-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                <button type="button" class="btn btn-danger btn-sm remove-faq-btn" title="Remove FAQ">
                                    <i class="ri-subtract-line"></i>
                                </button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Question</label>
                                <input type="text" class="form-control faq-question-input" name="faqs[${faqIndex}][question]" placeholder="Enter Question">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Answer</label>
                                <textarea class="form-control faq-answer-input" name="faqs[${faqIndex}][answer]" rows="4" placeholder="Enter Answer"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#faq_cards_row').append(html);
            $('#faq_cards_row .faq-item-col:last .faq-question-input').focus();
        });

        $(document).on('click', '.remove-faq-btn', function() {
            let col = $(this).closest('.faq-item-col');
            col.fadeOut(200, function() {
                $(this).remove();
            });
        });
    </script>
@endsection