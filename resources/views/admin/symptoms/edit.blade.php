@extends("admin.layouts.app")

<style>
    /* Make dropzone preview full width */
    #demo-upload .dz-preview {
        width: 100%;
        margin: 0;
    }

    /* Make image take full width */
    #demo-upload .dz-image {
        width: 100%;
        height: auto;
    }

    /* Ensure image scales properly */
    #demo-upload .dz-image img {
        width: 100%;
        height: auto;
        object-fit: contain; /* or cover if you want crop */
    }

    /* Optional: remove fixed height */
    #demo-upload .dz-image {
        height: auto !important;
    }

</style>

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
                <a href="{{ route("symptom-list") }}">Symptom List</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Edit Symptom
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="symptomFrm" method="post" action="{{ route("symptom-update") }}">
        <input type="hidden" name="symptom_id" value="{{ $symptomDetail->symptom_id }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Symptom</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row starts -->
                            <div class="row g-3">
                                <!-- Page Fields -->
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-xxl-12 col-lg-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="symptom_name">Symptom Name</label>
                                                <input type="text" class="form-control" id="symptom_name" name="symptom_name" placeholder="Enter Symptom Name" value="{{ $symptomDetail->symptom_name }}">
                                                <div class="invalid-feedback" id="msg_symptom_name"></div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-6 col-sm-6 mt-sm-2">
                                            <div class="mb-3">
                                                <label class="form-label" for="symptom_desc">Description</label>
                                                <textarea type="text" class="form-control" id="symptom_desc" name="symptom_desc" rows="2">{{ $symptomDetail->symptom_desc }}</textarea>
                                                <div class="invalid-feedback" id="msg_symptom_desc"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Page Image -->
                                <div class="col-lg-6">
                                    <form class="dropzone" id="image-upload" method="POST" action="{{ route('symptom-image-upload') }}" enctype="multipart/form-data">
                                        @csrf
                                        <label class="form-label">Symptom Icon/Image</label>
                                        <div class="dropzone dz-clickable" id="image-upload">
                                            <div class="dz-message">
                                                <button type="button" class="dz-button">
                                                    Click here to upload your photo
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <input type="hidden" name="symptom_image" id="symptom_image" value="{{ $symptomDetail->symptom_image }}">
                                </div>
                            </div>
                            <!-- Row ends -->

                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('symptom-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Update Symptom
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
        $('#symptomFrm').submit(function(e) {
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
        let existingImage = "{{ $symptomDetail->symptom_image ?? '' }}";
        let dz = new Dropzone("#image-upload", {
            url: "{{ route('symptom-image-upload') }}",
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
                            "{{ asset('uploads/symptom') }}/" + existingImage
                    );
                    myDropzone.emit("complete", mockFile);

                    myDropzone.files.push(mockFile);
                    document.getElementById('symptom_image').value = existingImage;
                }

                // Upload new image
                myDropzone.on("success", function(file, response) {
                    document.getElementById('symptom_image').value = response.filename;
                });

                // Remove image
                myDropzone.on("removedfile", function() {
                    document.getElementById('symptom_image').value = "";
                });
            }
        });
    </script>
@endsection
