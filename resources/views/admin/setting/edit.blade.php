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
                Edit Setting
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="settingFrm" method="post" action="{{ route('setting-update') }}">
        {{ csrf_field() }}
        <!-- App body starts -->
        <div class="app-body">

            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Custom tabs starts -->
                            <div class="custom-tabs-container">
                                <!-- Nav tabs starts -->
                                <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                    {{--<li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab" aria-controls="oneA" aria-selected="true"><i class="ri-home-5-line"></i> PersonalDetails</a>
                                    </li>--}}
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-twoA" data-bs-toggle="tab" href="#twoA" role="tab" aria-controls="twoA" aria-selected="false"><i class="ri-phone-line"></i> Contact</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="tab-threeA" data-bs-toggle="tab" href="#threeA" role="tab" aria-controls="threeA" aria-selected="false"><i class="ri-earth-line"></i> Global SEO Meta</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="tab-fourA" data-bs-toggle="tab" href="#fourA" role="tab" aria-controls="fourA" aria-selected="false"><i class="ri-fingerprint-line"></i> Social Media</a>
                                    </li>
                                </ul>
                                <!-- Nav tabs ends -->

                                <!-- Tab content starts -->
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="oneA" role="tabpanel">

                                        <!-- Row starts -->
                                        <div class="row gx-3">
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">First Name <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-account-circle-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a1" placeholder="Enter First Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Last Name <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-account-circle-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a2" placeholder="Enter Last Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a3">Age <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-flower-line"></i>
                                  </span>
                                                        <select class="form-select" id="a3">
                                                            <option value="0">Select Age</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="selectGender1">Gender<span
                                                                class="text-danger">*</span></label>
                                                    <div class="m-0">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="selectGenderOptions"
                                                                   id="selectGender1" value="male">
                                                            <label class="form-check-label" for="selectGender1">Male</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="selectGenderOptions"
                                                                   id="selectGender2" value="female">
                                                            <label class="form-check-label" for="selectGender2">Female</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a4">Create ID <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-secure-payment-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a4" placeholder="Create Unique ID">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a5">Email ID <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-mail-open-line"></i>
                                  </span>
                                                        <input type="email" class="form-control" id="a5" placeholder="Enter Email ID">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a6">Mobile Number <span
                                                                class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-phone-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a6" placeholder="Enter Mobile Number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Marital Status</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-vip-crown-2-line"></i>
                                  </span>
                                                        <select class="form-select" id="a7">
                                                            <option value="0">Select</option>
                                                            <option value="1">Married</option>
                                                            <option value="2">Un Married</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a8">Qualification</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-copper-diamond-line"></i>
                                  </span>
                                                        <select class="form-select" id="a8">
                                                            <option value="0">Select</option>
                                                            <option value="1">MBBS, MD</option>
                                                            <option value="2">MBBS, MS</option>
                                                            <option value="3">MBBS</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a9">Designation</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-nft-line"></i>
                                  </span>
                                                        <select class="form-select" id="a9">
                                                            <option value="0">Select</option>
                                                            <option value="1">Doctor</option>
                                                            <option value="2">Head of the Dept</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a10">Blood Group<span
                                                                class="text-danger">*</span></label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-drop-line"></i>
                                  </span>
                                                        <select class="form-select" id="a10">
                                                            <option value="0">Select</option>
                                                            <option value="1">A+</option>
                                                            <option value="2">A-</option>
                                                            <option value="3">B+</option>
                                                            <option value="4">B-</option>
                                                            <option value="5">O+</option>
                                                            <option value="6">O-</option>
                                                            <option value="7">AB+</option>
                                                            <option value="8">AB-</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a11">Address</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-projector-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a11" placeholder="Enter Address">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a12">Country</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-flag-line"></i>
                                  </span>
                                                        <select class="form-select" id="a12">
                                                            <option value="0">Select</option>
                                                            <option value="1">USA</option>
                                                            <option value="2">Canada</option>
                                                            <option value="3">Brazil</option>
                                                            <option value="4">India</option>
                                                            <option value="5">China</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a13">State</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-instance-line"></i>
                                  </span>
                                                        <select class="form-select" id="a13">
                                                            <option value="0">Select</option>
                                                            <option value="1">Alabama</option>
                                                            <option value="2">Alaska</option>
                                                            <option value="3">Arizona</option>
                                                            <option value="4">California</option>
                                                            <option value="5">Florida</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a14">City</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-scan-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a14" placeholder="Enter City">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a15">Postal Code</label>
                                                    <div class="input-group">
                                  <span class="input-group-text">
                                    <i class="ri-qr-scan-line"></i>
                                  </span>
                                                        <input type="text" class="form-control" id="a15" placeholder="Enter Postal Code">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Row ends -->

                                    </div>
                                    <div class="tab-pane fade show active" id="twoA" role="tabpanel">
                                        <!-- Row starts -->
                                        <div class="row gx-3">
                                            @foreach ($contactSetting as $key => $contact)
                                                <div class="{{ ($contact->setting_type == 'input' || $contact->setting_type == 'image') ? 'col-xxl-3' : 'col-xxl-6' }} col-lg-6 col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="settings['{{ $contact->setting_name }}']">{{ $contact->setting_label }}</label>
                                                        @if ($contact->setting_type == 'input')
                                                            <input type="text" class="form-control" name="settings[{{ $contact->setting_id }}]" value="{{ $contact->setting_value }}">
                                                        @elseif ($contact->setting_type == 'textarea')
                                                            <textarea type="text" class="form-control" name="settings[{{ $contact->setting_id }}]" rows="2">{{ $contact->setting_value }}</textarea>
                                                        @elseif ($contact->setting_type == 'editor')
                                                            <textarea id="settings_{{ $contact->setting_id }}" name="settings[{{ $contact->setting_id }}]">{{ $contact->setting_value }}</textarea>
                                                            <script>
                                                                CKEDITOR.replace( 'settings_{{ $contact->setting_id }}',
                                                                        {
                                                                            toolbar :
                                                                                    [
                                                                                        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source'] },
                                                                                        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                                                                                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                                                                                        { name: 'insert', items: [ 'Image' ] },
                                                                                        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
                                                                                        { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
                                                                                    ],
                                                                            height:200
                                                                        });
                                                            </script>
                                                        @endif
                                                        <div class="invalid-feedback" id="msg_category_meta_title"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Row ends -->
                                    </div>
                                    <div class="tab-pane fade" id="threeA" role="tabpanel">
                                        <!-- Row starts -->
                                        <div class="row gx-3">
                                            @foreach ($globalseoSetting as $key => $globalseo)
                                                <div class="{{ ($globalseo->setting_type == 'input' || $globalseo->setting_type == 'image') ? 'col-xxl-6' : 'col-xxl-12' }} col-lg-6 col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="settings['{{ $globalseo->setting_name }}']">{{ $globalseo->setting_label }}</label>
                                                        @if ($globalseo->setting_type == 'input')
                                                            <input type="text" class="form-control" name="settings[{{ $globalseo->setting_id }}]" value="{{ $globalseo->setting_value }}">
                                                        @elseif ($globalseo->setting_type == 'textarea')
                                                            <textarea type="text" class="form-control" name="settings[{{ $globalseo->setting_id }}]" rows="2">{{ $globalseo->setting_value }}</textarea>
                                                        @elseif ($globalseo->setting_type == 'editor')
                                                            <textarea id="settings_{{ $globalseo->setting_id }}" name="settings[{{ $globalseo->setting_id }}]">{{ $globalseo->setting_value }}</textarea>
                                                            <script>
                                                                CKEDITOR.replace( 'settings_{{ $globalseo->setting_id }}',
                                                                        {
                                                                            toolbar :
                                                                                    [
                                                                                        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source'] },
                                                                                        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                                                                                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                                                                                        { name: 'insert', items: [ 'Image' ] },
                                                                                        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
                                                                                        { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
                                                                                    ],
                                                                            height:200
                                                                        });
                                                            </script>
                                                        @endif
                                                        <div class="invalid-feedback" id="msg_category_meta_title"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Row ends -->
                                    </div>
                                    <div class="tab-pane fade" id="fourA" role="tabpanel">
                                        <!-- Row starts -->
                                        <div class="row gx-3">
                                            @foreach ($socialSetting as $key => $social)
                                                <div class="{{ ($social->setting_type == 'input' || $social->setting_type == 'image') ? 'col-xxl-4' : 'col-xxl-12' }} col-lg-6 col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="settings['{{ $social->setting_name }}']">{{ $social->setting_label }}</label>
                                                        @if ($social->setting_type == 'input')
                                                            <input type="text" class="form-control" name="settings[{{ $social->setting_id }}]" value="{{ $social->setting_value }}">
                                                        @elseif ($social->setting_type == 'textarea')
                                                            <textarea type="text" class="form-control" name="settings[{{ $social->setting_id }}]" rows="2">{{ $social->setting_value }}</textarea>
                                                        @elseif ($social->setting_type == 'editor')
                                                            <textarea id="settings_{{ $social->setting_id }}" name="settings[{{ $social->setting_id }}]">{{ $social->setting_value }}</textarea>
                                                            <script>
                                                                CKEDITOR.replace( 'settings_{{ $social->setting_id }}',
                                                                        {
                                                                            toolbar :
                                                                                    [
                                                                                        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source'] },
                                                                                        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                                                                                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                                                                                        { name: 'insert', items: [ 'Image' ] },
                                                                                        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
                                                                                        { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
                                                                                    ],
                                                                            height:200
                                                                        });
                                                            </script>
                                                        @endif
                                                        <div class="invalid-feedback" id="msg_category_meta_title"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Row ends -->
                                    </div>
                                </div>
                                <!-- Tab content ends -->
                            </div>
                            <!-- Custom tabs ends -->

                            <!-- Buttons starts -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('setting') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" name="submit" class="btn btn-primary">
                                    Update Setting
                                </button>
                            </div>
                            <!-- Buttons ends -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->

        </div>
        <!-- App body ends -->
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

        $('#settingFrm').submit(function(e) {
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
    </script>
@endsection