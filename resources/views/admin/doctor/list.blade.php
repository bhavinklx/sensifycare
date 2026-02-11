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
                Doctor List
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <div class="app-body">
        <!-- Row starts -->
        <div class="row gx-3">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title">Doctor List</h5>
                        @if (auth()->user()->can('patient-add'))
                            <a href="{{ route("patient-add") }}" class="btn btn-primary ms-auto">Add Blog</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <!-- Table starts -->
                        <div class="table-responsive">
                            <table id="basicExample" class="table m-0 align-middle">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" value="" id="checkall" name="checkall">
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Blood Group</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody id="tablecontents" />
                            </table>
                        </div>
                        <!-- Table ends -->

                        <!-- Modal Delete Row -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="delRowLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="delRowLabel">
                                            Confirm
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="doctor_id">
                                        Are you sure you want to delete?
                                    </div>
                                    <div class="modal-footer">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">No</button>
                                            <button class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close" onclick="deleteData()">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row ends -->
    </div>
    <!-- App body ends -->
@endsection

@section('page-js')
    <script type="text/javascript">
        $(document).ready(function(){
            $("#checkall").click(function(){
                if(this.checked){
                    $(".check_class").attr("checked",true);
                    $(".check_class").parent().addClass("checked");
                }else{
                    $(".check_class").attr("checked",false);
                    $(".check_class").parent().removeClass("checked");
                }
            });
            $("#status_msg").hide();
            $("#alert_msg").hide();
        });

        var table = $('#basicExample').DataTable({
            pageLength: 25,
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: true,
            autoWidth: false,
            ajax: '{{ route("doctor-load-table") }}',
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'uid', name: 'doctor_uid' },
                { data: 'title', name: 'doctor_fname' },
                { data: 'designation', name: 'doctor_designation' },
                { data: 'email', name: 'doctor_email' },
                { data: 'phone', name: 'doctor_phone' },
                { data: 'gender', name: 'doctor_gender' },
                { data: 'age', name: 'doctor_age' },
                { data: 'blood_group', name: 'doctor_blood_group' },
                { data: 'date', name: 'created_at' },
                { data: 'status', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ],
            language: {
                lengthMenu: "Display _MENU_ Records Per Page",
                info: "Showing Page _PAGE_ of _PAGES_",
            },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $( "#tablecontents" ).sortable({
            items: "tr",
            cursor: 'move',
            opacity: 0.8,
            update: function() {
                sendOrderToServer();
            }
        });

        function sendOrderToServer() {
            var order = [];
            $('tr.row1').each(function(index, element) {
                order.push({
                    doctor_id: $(this).attr('data-id'),
                    position: index + 1
                });
            });
            //alert(order)
            $.ajax({
                url: "{{ route('doctor-update-order') }}",
                type: "POST",
                //dataType: "json",
                data: {
                    order:order,
                    _token: '{{csrf_token()}}'
                },
                success: function(response) {
                    $.toast({
                        heading: response
                        , position: 'top-right'
                        , loaderBg: '#ff6849'
                        , icon: 'success'
                        , hideAfter: 3500
                        , stack: 6
                    });
                }
            });

        }

        function change_status(doctor_id, status) {
            $.ajax({
                url: "{{ route('doctor-change-status') }}",
                method: "POST",
                data: {
                    doctor_id:doctor_id,
                    status:status,
                    _token:"{{ csrf_token() }}"
                },
                success: function (response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',      // top-right corner
                        icon: 'success',          // success, error, warning, info
                        title: response,          // message text
                        showConfirmButton: false, // no OK button
                        timer: 3500,              // auto close after 3.5 seconds
                        timerProgressBar: true,
                        padding: '0.5em 1em',      // smaller padding
                    });
                    if (status == 1){
                        $("#td_status_"+doctor_id).html("<a href=\"javascript:void(0)\" onclick=\"change_status('"+doctor_id+"', '0')\" ><span class=\"badge bg-success\">Active</span></a>");
                    } else {
                        $("#td_status_"+doctor_id).html("<a href=\"javascript:void(0)\" onclick=\"change_status('"+doctor_id+"', '1')\" ><span class=\"badge bg-danger\">Inactive</span></a>");
                    }
                }
            });
        }

        function openDeleteModal(doctor_id) {
            $('#doctor_id').val(doctor_id);
            $('#deleteModal').modal('show');
        }

        function deleteData() {
            let doctor_id = $('#doctor_id').val();
            $.ajax({
                url: "{{ route('doctor-delete') }}",
                type: "POST",
                data: {
                    _token:'{{ csrf_token() }}',
                    doctor_id:doctor_id
                },
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    /*$('#row_' + doctor_id).remove();
                     setTimeout(function(){
                     location.reload();
                     },2000);*/
                    table.row($('#row_' + doctor_id))
                            .remove()
                            .draw(false);
                }
            });
        }
    </script>
@endsection
