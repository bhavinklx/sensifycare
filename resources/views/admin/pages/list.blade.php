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
                Page List
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
                        <h5 class="card-title">Page List</h5>
                        @if (auth()->user()->can('pages-add'))
                            <a href="{{ route('pages-add') }}" class="btn btn-primary ms-auto">Add Page</a>
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
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkall" name="checkall">
                                            </div>
                                        </th>
                                        <th>Title</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Header Status</th>
                                        <th>Footer Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tablecontents">
                                @foreach($pagesDetail as $pages)
                                    <tr class="row1" data-id="{{ $pages->page_id }}" id="row_{{ $pages->page_id }}">
                                        <td>
                                            <div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="{{ $pages->page_id }}"></div>
                                        </td>
                                        <td>{{ $pages->page_title }}</td>
                                        <td>{{ date('d-m-Y h:i:s A', strtotime($pages->created_at)) }}</td>
                                        <td>
                                            @if($pages->page_status=='1')
                                                <div id="td_status_{{ $pages->page_id }}"><a href="javascript:void(0)" onclick="change_status('{{ $pages->page_id }}',0)" ><span class="badge bg-success">Active</span></a></div>
                                            @else
                                                <div id="td_status_{{ $pages->page_id }}"><a href="javascript:void(0)" onclick="change_status('{{ $pages->page_id }}',1)" ><span class="badge bg-danger">Inactive</span></a></div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pages->page_header_status=='1')
                                                <div id="td_header_status_{{ $pages->page_id }}"><a href="javascript:void(0)" onclick="change_header_status('{{ $pages->page_id }}',0)" ><span class="badge bg-success">Active</span></a></div>
                                            @else
                                                <div id="td_header_status_{{ $pages->page_id }}"><a href="javascript:void(0)" onclick="change_header_status('{{ $pages->page_id }}',1)" ><span class="badge bg-danger">Inactive</span></a></div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pages->page_footer_status=='1')
                                                <div id="td_footer_status_{{ $pages->page_id }}"><a href="javascript:void(0)" onclick="change_footer_status('{{ $pages->page_id }}',0)" ><span class="badge bg-success">Active</span></a></div>
                                            @else
                                                <div id="td_footer_status_{{ $pages->page_id }}"><a href="javascript:void(0)" onclick="change_footer_status('{{ $pages->page_id }}',1)" ><span class="badge bg-danger">Inactive</span></a></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                @if($pages->page_id > 1)
                                                    {{--@if(auth()->user()->can('pages-delete'))--}}
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteSingal('{{ $pages->page_id }}');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Page"> <i class="ri-delete-bin-line"></i> </button>
                                                    <a href="javascript:void(0)" data-toggle="tooltip" onclick="deleteSingal('{{ $pages->page_id }}');" data-placement="top" title="Delete" > <i class="fa fa-trash text-danger"></i> </a>
                                                    {{--@endif--}}
                                                    {{--@if(auth()->user()->can('pages-edit'))--}}
                                                    <a href="{{ route("pages-edit", ['id' => $pages->page_id]) }}" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Page"> <i class="ri-edit-box-line"></i> </a>
                                                    {{--@endif--}}
                                                @else
                                                    {{--@if(auth()->user()->can('pages-delete'))--}}
                                                    <a href="{{ route("pages-edit", ['id' => $pages->page_id]) }}" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Page"> <i class="ri-edit-box-line"></i> </a>
                                                    {{--@endif--}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($pages->subPages as $subPages)
                                        <tr class="row1" data-id="{{ $subPages->page_id }}" id="row_{{ $subPages->page_id }}">
                                            <td>
                                                <div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="{{ $subPages->page_id }}"></div>
                                            </td>
                                            <td>{{ $pages->page_title }}</td>
                                            <td>{{ date('d-m-Y h:i:s A', strtotime($pages->created_at)) }}</td>
                                            <td>
                                                @if ($pages->page_status == '1')
                                                    <div id="td_status_{{ $pages->page_id }}"><a href="javascript:void(0)"
                                                            onclick="change_status('{{ $pages->page_id }}',0)"><span
                                                                class="badge bg-success">Active</span></a></div>
                                                @else
                                                    <div id="td_status_{{ $pages->page_id }}"><a href="javascript:void(0)"
                                                            onclick="change_status('{{ $pages->page_id }}',1)"><span
                                                                class="badge bg-danger">Inactive</span></a></div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pages->page_header_status == '1')
                                                    <div id="td_header_status_{{ $pages->page_id }}"><a
                                                            href="javascript:void(0)"
                                                            onclick="change_header_status('{{ $pages->page_id }}',0)"><span
                                                                class="badge bg-success">Active</span></a></div>
                                                @else
                                                    <div id="td_header_status_{{ $pages->page_id }}"><a
                                                            href="javascript:void(0)"
                                                            onclick="change_header_status('{{ $pages->page_id }}',1)"><span
                                                                class="badge bg-danger">Inactive</span></a></div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pages->page_footer_status == '1')
                                                    <div id="td_footer_status_{{ $pages->page_id }}"><a
                                                            href="javascript:void(0)"
                                                            onclick="change_footer_status('{{ $pages->page_id }}',0)"><span
                                                                class="badge bg-success">Active</span></a></div>
                                                @else
                                                    <div id="td_footer_status_{{ $pages->page_id }}"><a
                                                            href="javascript:void(0)"
                                                            onclick="change_footer_status('{{ $pages->page_id }}',1)"><span
                                                                class="badge bg-danger">Inactive</span></a></div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-inline-flex gap-1">
                                                    @if ($pages->page_id > 1)
                                                        {{-- @if (auth()->user()->can('pages-delete')) --}}
                                                        <button class="btn btn-outline-danger btn-sm"
                                                            onclick="deleteData('{{ $pages->page_id }}');"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="Delete Page"> <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                                            onclick="deleteData('{{ $pages->page_id }}');"
                                                            data-placement="top" title="Delete"> <i
                                                                class="fa fa-trash text-danger"></i> </a>
                                                        {{-- @endif --}}
                                                        {{-- @if (auth()->user()->can('pages-edit')) --}}
                                                        <a href="{{ route('pages-edit', ['id' => $pages->page_id]) }}"
                                                            class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" data-bs-title="Edit Page"> <i
                                                                class="ri-edit-box-line"></i> </a>
                                                        {{-- @endif --}}
                                                    @else
                                                        {{-- @if (auth()->user()->can('pages-delete')) --}}
                                                        <a href="{{ route('pages-edit', ['id' => $pages->page_id]) }}"
                                                            class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" data-bs-title="Edit Page"> <i
                                                                class="ri-edit-box-line"></i> </a>
                                                        {{-- @endif --}}
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach ($pages->subPages as $subPages)
                                            <tr>
                                                <td>
                                                    <div class="form-check m-0"> <input class="form-check-input check_class"
                                                            type="checkbox" id="check[]" name="check[]"
                                                            value="{{ $subPages->page_id }}"></div>
                                                </td>
                                                <td>&nbsp; <img src="{{ url('assets/img/arrow-01.gif') }}">&nbsp;
                                                    {{ $subPages->page_title }}</td>
                                                <td>{{ date('d-m-Y h:i:s A', strtotime($subPages->created_at)) }}</td>
                                                <td>
                                                    @if ($subPages->page_status == '1')
                                                        <div id="td_status_{{ $subPages->page_id }}"><a
                                                                href="javascript:void(0)"
                                                                onclick="change_status('{{ $subPages->page_id }}',0)"><span
                                                                    class="badge bg-success">Active</span></a></div>
                                                    @else
                                                        <div id="td_status_{{ $subPages->page_id }}"><a
                                                                href="javascript:void(0)"
                                                                onclick="change_status('{{ $subPages->page_id }}',1)"><span
                                                                    class="badge bg-danger">Inactive</span></a></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($subPages->page_header_status == '1')
                                                        <div id="td_header_status_{{ $subPages->page_id }}"><a
                                                                href="javascript:void(0)"
                                                                onclick="change_header_status('{{ $subPages->page_id }}',0)"><span
                                                                    class="badge bg-success">Active</span></a></div>
                                                    @else
                                                        <div id="td_header_status_{{ $subPages->page_id }}"><a
                                                                href="javascript:void(0)"
                                                                onclick="change_header_status('{{ $subPages->page_id }}',1)"><span
                                                                    class="badge bg-danger">Inactive</span></a></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($subPages->page_footer_status == '1')
                                                        <div id="td_footer_status_{{ $subPages->page_id }}"><a
                                                                href="javascript:void(0)"
                                                                onclick="change_footer_status('{{ $subPages->page_id }}',0)"><span
                                                                    class="badge bg-success">Active</span></a></div>
                                                    @else
                                                        <div id="td_footer_status_{{ $subPages->page_id }}"><a
                                                                href="javascript:void(0)"
                                                                onclick="change_footer_status('{{ $subPages->page_id }}',1)"><span
                                                                    class="badge bg-danger">Inactive</span></a></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-inline-flex gap-1">
                                                        {{-- @if (auth()->user()->can('pages-delete')) --}}
                                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                                            onclick="deleteData('{{ $subPages->page_id }}');"
                                                            data-placement="top" title="Delete"> <i
                                                                class="fa fa-trash text-danger"></i> </a>
                                                        {{-- @endif --}}
                                                        {{-- @if (auth()->user()->can('pages-edit')) --}}
                                                        <a href="{{ route('pages-edit', ['id' => $subPages->page_id]) }}"
                                                            class="btn btn-outline-success btn-sm"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="Edit Page"> <i class="ri-edit-box-line"></i>
                                                        </a>
                                                        {{-- @endif --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Table ends -->

                        <!-- Modal Delete Row -->
                        <div class="modal fade" id="delRow" tabindex="-1" aria-labelledby="delRowLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="delRowLabel">
                                            Confirm
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="page_id">
                                        Are you sure you want to delete?
                                    </div>
                                    <div class="modal-footer">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">No</button>
                                            <button class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Yes</button>
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
        $(document).ready(function() {
            $(document).on('click', '#confirmDeleteBtn', function() {
                $.ajax({
                    url: "{{ route('pages-delete') }}",
                    type: "POST",
                    data: {
                        page_id: deletePageId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        if (response.status === true) {

                            let table = $('#basicExample').DataTable();

                            // ✅ REMOVE ROW BY ID
                            table
                                .row('#page-row-' + deletePageId)
                                .remove()
                                .draw(false);

                            toastr.success(response.message);
                        }
                    }
                });
            });

            let message = localStorage.getItem('toast_success');
            if (message) {
                toastr.success(message);
                localStorage.removeItem('toast_success');
            }
        });

        var page_length = 25;
        var table = $("#basicExample").DataTable({
            pageLength: 25,
            language: {
                lengthMenu: "Display _MENU_ Records Per Page",
                info: "Showing Page _PAGE_ of _PAGES_",
            },
        });

        $("#tablecontents").sortable({
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
                    page_id: $(this).attr('data-id'),
                    position: index + 1
                });
            });
            //alert(order)
            $.ajax({
                url: "{{ route('pages-update-order') }}",
                type: "POST",
                //dataType: "json",
                data: {
                    order: order,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $.toast({
                        heading: response,
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'success',
                        hideAfter: 3500,
                        stack: 6
                    });
                }
            });

        }

        function change_status(page_id, status) {
            $.ajax({
                url: "{{ route('pages-change-status') }}",
                method: "POST",
                data: {
                    page_id: page_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end', // top-right corner
                        icon: 'success', // success, error, warning, info
                        title: response, // message text
                        showConfirmButton: false, // no OK button
                        timer: 3500, // auto close after 3.5 seconds
                        timerProgressBar: true,
                        padding: '0.5em 1em', // smaller padding
                    });
                    if (status == 1) {
                        $("#td_status_" + page_id).html(
                            "<a href=\"javascript:void(0)\" onclick=\"change_status('" + page_id +
                            "', '0')\" ><span class=\"badge bg-success\">Active</span></a>");
                    } else {
                        $("#td_status_" + page_id).html(
                            "<a href=\"javascript:void(0)\" onclick=\"change_status('" + page_id +
                            "', '1')\" ><span class=\"badge bg-danger\">Inactive</span></a>");
                    }
                }
            });
        }

        function change_header_status(page_id, status) {
            $.ajax({
                url: "{{ route('pages-change-header-status') }}",
                method: "POST",
                data: {
                    page_id: page_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end', // top-right corner
                        icon: 'success', // success, error, warning, info
                        title: response, // message text
                        showConfirmButton: false, // no OK button
                        timer: 3500, // auto close after 3.5 seconds
                        timerProgressBar: true,
                        padding: '0.5em 1em', // smaller padding
                    });
                    if (status == 1) {
                        $("#td_header_status_" + page_id).html(
                            "<a href=\"javascript:void(0)\" onclick=\"change_header_status('" + page_id +
                            "', '0')\" ><span class=\"badge bg-success\">Active</span></a>");
                    } else {
                        $("#td_header_status_" + page_id).html(
                            "<a href=\"javascript:void(0)\" onclick=\"change_header_status('" + page_id +
                            "', '1')\" ><span class=\"badge bg-danger\">Inactive</span></a>");
                    }
                }
            });
        }

        function change_footer_status(page_id, status) {
            $.ajax({
                url: "{{ route('pages-change-footer-status') }}",
                method: "POST",
                data: {
                    page_id: page_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end', // top-right corner
                        icon: 'success', // success, error, warning, info
                        title: response, // message text
                        showConfirmButton: false, // no OK button
                        timer: 3500, // auto close after 3.5 seconds
                        timerProgressBar: true,
                        padding: '0.5em 1em', // smaller padding
                    });
                    if (status == 1) {
                        $("#td_footer_status_" + page_id).html(
                            "<a href=\"javascript:void(0)\" onclick=\"change_footer_status('" + page_id +
                            "', '0')\" ><span class=\"badge bg-success\">Active</span></a>");
                    } else {
                        $("#td_footer_status_" + page_id).html(
                            "<a href=\"javascript:void(0)\" onclick=\"change_footer_status('" + page_id +
                            "', '1')\" ><span class=\"badge bg-danger\">Inactive</span></a>");
                    }
                }
            });
        }

        function deleteData(page_id) {
            $.ajax({
                url: "{{ route('pages-delete') }}",
                type: "POST",
                data: {
                    _token:'{{ csrf_token() }}',
                    page_id:page_id,
                },
                success: function (response) {
                }
            });
        }
    </script>
@endsection
