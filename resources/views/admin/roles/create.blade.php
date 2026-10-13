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
                Add Role
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->
    <!-- App body starts -->
    <form id="roleFrm" method="post" action="{{ route('role-insert') }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Add Role</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="name">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Role Name">
                                <div class="invalid-feedback" id="msg_name"></div>
                            </div>

                            <!-- Permission Section starts -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Permissions</label>

                                <!-- Top Row: Check All & Column Quick Toggles -->
                                <div class="row align-items-center mb-3">
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkall">
                                            <label class="form-check-label fw-bold" for="checkall">
                                                Check All
                                            </label>
                                        </div>
                                    </div>
                                    @foreach ($actionKeys as $actKey => $actLabel)
                                        <div class="col">
                                            <div class="form-check">
                                                <input class="form-check-input col-action-check" type="checkbox" id="col_check_{{ $actKey }}" data-action="{{ $actKey }}">
                                                <label class="form-check-label fw-semibold" for="col_check_{{ $actKey }}">
                                                    {{ $actLabel }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Header Row: Column Labels -->
                                <div class="row align-items-center mb-2">
                                    <div class="col-4">
                                        <span class="fw-bold">Module</span>
                                    </div>
                                    @foreach ($actionKeys as $actKey => $actLabel)
                                        <div class="col">
                                            <span class="fw-bold">{{ $actLabel }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Divider Line -->
                                <div class="row">
                                    <div class="col-12">
                                        <hr class="my-2">
                                    </div>
                                </div>

                                <!-- Module Rows -->
                                @if ($groupedPermissions && count($groupedPermissions) > 0)
                                    @foreach ($groupedPermissions as $modSlug => $moduleData)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input row-module-check" 
                                                           type="checkbox" 
                                                           id="mod_check_{{ $modSlug }}" 
                                                           data-module="{{ $modSlug }}">
                                                    <label class="form-check-label" for="mod_check_{{ $modSlug }}">
                                                        {{ $moduleData['label'] }}
                                                    </label>
                                                </div>
                                            </div>
                                            @foreach ($actionKeys as $actKey => $actLabel)
                                                <div class="col">
                                                    @if (isset($moduleData['actions'][$actKey]))
                                                        @php $perm = $moduleData['actions'][$actKey]; @endphp
                                                        <div class="form-check">
                                                            <input class="form-check-input check_class permission-check" 
                                                                   type="checkbox" 
                                                                   id="permission{{ $perm->id }}" 
                                                                   name="permission[]" 
                                                                   value="{{ $perm->name }}"
                                                                   data-module="{{ $modSlug }}"
                                                                   data-action="{{ $actKey }}">
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif

                                <div class="invalid-feedback d-block" id="msg_permission"></div>
                            </div>
                            <!-- Permission Section ends -->

                            <div class="col-sm-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route("role-list") }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Add Role
                                    </button>
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
        $(document).ready(function(){
            // Function to synchronize row, column and master checkboxes
            function syncCheckboxes() {
                // 1. Sync Row checkboxes
                $('.row-module-check').each(function() {
                    let module = $(this).data('module');
                    let rowPerms = $('.permission-check[data-module="' + module + '"]');
                    let checkedRowPerms = rowPerms.filter(':checked');
                    $(this).prop('checked', rowPerms.length > 0 && rowPerms.length === checkedRowPerms.length);
                });

                // 2. Sync Column checkboxes
                $('.col-action-check').each(function() {
                    let action = $(this).data('action');
                    let colPerms = $('.permission-check[data-action="' + action + '"]');
                    let checkedColPerms = colPerms.filter(':checked');
                    $(this).prop('checked', colPerms.length > 0 && colPerms.length === checkedColPerms.length);
                });

                // 3. Sync Master Check All
                let totalPerms = $('.permission-check').length;
                let checkedPerms = $('.permission-check:checked').length;
                $('#checkall').prop('checked', totalPerms > 0 && totalPerms === checkedPerms);
            }

            // Master "Check All"
            $('#checkall').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('.permission-check').prop('checked', isChecked);
                $('.row-module-check').prop('checked', isChecked);
                $('.col-action-check').prop('checked', isChecked);
            });

            // Column-wise Checkbox (e.g. Add, Edit, Delete, View)
            $('.col-action-check').on('change', function() {
                let action = $(this).data('action');
                let isChecked = $(this).is(':checked');
                $('.permission-check[data-action="' + action + '"]').prop('checked', isChecked);
                syncCheckboxes();
            });

            // Row-wise Checkbox (e.g. Module row)
            $('.row-module-check').on('change', function() {
                let module = $(this).data('module');
                let isChecked = $(this).is(':checked');
                $('.permission-check[data-module="' + module + '"]').prop('checked', isChecked);
                syncCheckboxes();
            });

            // Individual Permission Checkbox
            $(document).on('change', '.permission-check', function() {
                syncCheckboxes();
            });

            syncCheckboxes();
            $("#status_msg").hide();
            $("#alert_msg").hide();
        });

        $('#roleFrm').submit(function(e) {
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
                            $('#msg_' + key).html(val[0]).css('display', 'block');
                        });
                    }
                    $('#loading-wrapper').fadeOut(200);
                }
            });
        });
    </script>
@endsection
