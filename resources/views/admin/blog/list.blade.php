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
                Blog List
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
                        <h5 class="card-title">Blog List</h5>
                        @if (auth()->user()->can('blog-add'))
                            <a href="{{ route("blog-add") }}" class="btn btn-primary ms-auto">Add Blog</a>
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
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
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
                                        <input type="hidden" id="blog_id">
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
            ajax: '{{ route("blog-load-table") }}',
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'title', name: 'blog_title' },
                { data: 'image', orderable: false, searchable: false },
                { data: 'category', orderable: false, searchable: false },
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

        $(document).ready(function () {
            $("#tablecontents").sortable({
                items: "tr",
                cursor: "move",
                opacity: 0.8,
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index) {
                        $(this).width($originals.eq(index).width());
                    });
                    return $helper;
                },
                update: function () {
                    sendOrderToServer();
                }
            });
        });

        function sendOrderToServer() {
            var order = [];
            $('#tablecontents tr.row1').each(function(index) {
                order.push({
                    blog_id: $(this).data('id'),
                    position: index + 1
                });
            });

            //alert(order)
            $.ajax({
                url: "{{ route('blog-update-order') }}",
                type: "POST",
                //dataType: "json",
                data: {
                    order:order,
                    _token: '{{csrf_token()}}'
                },
                success: function(response) {
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
                }
            });
        }

        function change_status(blog_id, status) {
            $.ajax({
                url: "{{ route('blog-change-status') }}",
                method: "POST",
                data: {
                    blog_id:blog_id,
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
                        $("#td_status_"+blog_id).html("<a href=\"javascript:void(0)\" onclick=\"change_status('"+blog_id+"', '0')\" ><span class=\"badge bg-success\">Active</span></a>");
                    } else {
                        $("#td_status_"+blog_id).html("<a href=\"javascript:void(0)\" onclick=\"change_status('"+blog_id+"', '1')\" ><span class=\"badge bg-danger\">Inactive</span></a>");
                    }
                }
            });
        }

        function openDeleteModal(blog_id) {
            $('#blog_id').val(blog_id);
            $('#deleteModal').modal('show');
        }

        function deleteData() {
            let blog_id = $('#blog_id').val();
            $.ajax({
                url: "{{ route('blog-delete') }}",
                type: "POST",
                data: {
                    _token:'{{ csrf_token() }}',
                    blog_id:blog_id
                },
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    /*$('#row_' + blog_id).remove();
                     setTimeout(function(){
                     location.reload();
                     },2000);*/
                    table
                            .row($('#row_' + blog_id))
                            .remove()
                            .draw(false);
                }
            });
        }
    </script>
@endsection
