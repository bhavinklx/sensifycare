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
                FAQ List
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
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="card-title m-0">FAQ List</h5>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <!-- Category Filter Dropdown -->
                            <div class="d-flex align-items-center gap-2">
                                <label for="filter_category" class="form-label m-0 text-nowrap small text-muted">Category:</label>
                                <select class="form-select form-select-sm" id="filter_category" style="min-width: 170px;">
                                    <option value="all">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->fcategory_id }}">{{ $category->fcategory_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (auth()->user()->can('faq-add'))
                                <a href="{{ route("faq-add") }}" class="btn btn-primary btn-sm">Add FAQ</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Table starts -->
                        <div class="table-responsive">
                            <table id="faqTable" class="table m-0 align-middle">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" value="" id="checkall" name="checkall">
                                        </div>
                                    </th>
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
                                        <input type="hidden" id="faq_id">
                                        Are you sure you want to delete this FAQ?
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
                    $(".check_class").attr("checked", true);
                    $(".check_class").parent().addClass("checked");
                } else {
                    $(".check_class").attr("checked", false);
                    $(".check_class").parent().removeClass("checked");
                }
            });
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
                    faq_id: $(this).data('id'),
                    position: index + 1
                });
            });

            $.ajax({
                url: "{{ route('faq-update-order') }}",
                type: "POST",
                data: {
                    order: order,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        padding: '0.5em 1em',
                    });
                }
            });
        }

        var table = $('#faqTable').DataTable({
            pageLength: 25,
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: true,
            autoWidth: false,
            ajax: {
                url: '{{ route("faq-load-table") }}',
                data: function(d) {
                    d.fcategory_id = $('#filter_category').val();
                }
            },
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'category', name: 'category.fcategory_title' },
                { data: 'date', name: 'created_at' },
                { data: 'status', name: 'faq_status', orderable: false, searchable: false },
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

        $('#filter_category').change(function() {
            table.draw();
        });

        function change_status(faq_id, status) {
            $.ajax({
                url: "{{ route('faq-change-status') }}",
                method: "POST",
                data: {
                    faq_id: faq_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        padding: '0.5em 1em',
                    });
                    if (status == 1){
                        $("#td_status_" + faq_id).html("<a href=\"javascript:void(0)\" onclick=\"change_status('" + faq_id + "', '0')\"><span class=\"badge bg-success\">Active</span></a>");
                    } else {
                        $("#td_status_" + faq_id).html("<a href=\"javascript:void(0)\" onclick=\"change_status('" + faq_id + "', '1')\"><span class=\"badge bg-danger\">Inactive</span></a>");
                    }
                }
            });
        }

        function openDeleteModal(faq_id) {
            $('#faq_id').val(faq_id);
            $('#deleteModal').modal('show');
        }

        function deleteData() {
            let faq_id = $('#faq_id').val();
            $.ajax({
                url: "{{ route('faq-delete') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    faq_id: faq_id,
                },
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    table
                        .row($('#row_' + faq_id))
                        .remove()
                        .draw(false);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'FAQ deleted successfully',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        padding: '0.5em 1em',
                    });
                }
            });
        }
    </script>
@endsection
