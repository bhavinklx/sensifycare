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
                Edit Blog Category
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="bcategoryFrm" method="post" action="{{ route("bcategory-update") }}">
        <input type="hidden" name="bcategory_id" value="{{ $bcategoryDetail->bcategory_id }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Blog Category</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row gx-3">
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Category Title</label>
                                        <input type="text" class="form-control" id="bcategory_title" name="bcategory_title" placeholder="Enter Category Title" value="{{ $bcategoryDetail->bcategory_title }}">
                                        <div class="invalid-feedback" id="msg_bcategory_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="phone">Category Slug</label>
                                        <input type="text" class="form-control" id="bcategory_slug" name="bcategory_slug" placeholder="Enter Category Slug" value="{{ $bcategoryDetail->bcategory_slug }}">
                                        <div class="invalid-feedback" id="msg_bcategory_slug"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="bcategory_meta_title">Meta Title</label>
                                        <input type="text" class="form-control" id="bcategory_meta_title" name="bcategory_meta_title" placeholder="Enter Meta Title" value="{{ $bcategoryDetail->bcategory_meta_title }}">
                                        <div class="invalid-feedback" id="msg_bcategory_meta_title"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="bcategory_meta_keyword">Meta Keyword</label>
                                        <input type="text" class="form-control" id="bcategory_meta_keyword" name="bcategory_meta_keyword" placeholder="Meta Keyword" value="{{ $bcategoryDetail->bcategory_meta_keyword }}">
                                        <div class="invalid-feedback" id="msg_bcategory_meta_keyword"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-12 col-lg-12 col-sm-6 mt-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label" for="bcategory_meta_desc">Meta Description</label>
                                        <textarea type="text" class="form-control" id="bcategory_meta_desc" name="bcategory_meta_desc" rows="2">{{ $bcategoryDetail->bcategory_meta_desc }}</textarea>
                                        <div class="invalid-feedback" id="msg_bcategory_meta_desc"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('bcategory-list') }}" class="btn btn-outline-secondary">
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
        $('#bcategory_title').keyup(function(e) {
            $.ajax({
                url: "{{ route('bcategory-create-slug') }}",
                type: "GET",
                data: {
                    'bcategory_title': $(this).val()
                },
                success: function(response) {
                    $('#bcategory_slug').val(response.slug);
                }
            });
        });
        
        $('#bcategoryFrm').submit(function(e) {
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
