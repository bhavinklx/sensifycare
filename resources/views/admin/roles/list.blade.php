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
                Role List
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
                        <h5 class="card-title">Role List</h5>
                        @if (auth()->user()->can('role-add'))
                            <a href="{{ route("role-add") }}" class="btn btn-primary ms-auto">Add Role</a>
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
                                    <th>Title</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
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
                                        <input type="hidden" id="role_id">
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
    <script type="application/javascript">
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
            ajax: '{{ route("role-load-table") }}',
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'name' },
                { data: 'date', name: 'date' },
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

        function openDeleteModal(role_id) {
            $('#role_id').val(role_id);
            $('#deleteModal').modal('show');
        }

        function deleteData() {
            let role_id = $('#role_id').val();
            $.ajax({
                url: "{{ route('role-delete') }}",
                type: "POST",
                data: {
                    _token:'{{ csrf_token() }}',
                    role_id:role_id
                },
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    /*$('#row_' + role_id).remove();
                     setTimeout(function(){
                     location.reload();
                     },2000);*/
                    table
                            .row($('#row_' + role_id))
                            .remove()
                            .draw(false);
                }
            });
        }
    </script>
@endsection
