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

                        <!-- Modal View Blog Details -->
                        <div class="modal fade" id="blogViewModal" tabindex="-1" aria-labelledby="blogViewModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light py-3 px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-2 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="ri-article-line fs-5"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold mb-0" id="blogViewModalLabel">Blog Preview</h5>
                                                <small class="text-muted">Viewing blog details and formatted description</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- Loader -->
                                        <div id="blogViewModalLoader" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="text-muted mt-2 small">Loading blog content...</div>
                                        </div>

                                        <!-- Error Message -->
                                        <div id="blogViewModalError" class="alert alert-danger d-none my-3" role="alert">
                                            <i class="ri-error-warning-line me-1"></i> Failed to load blog details. Please try again.
                                        </div>

                                        <!-- Blog Content -->
                                        <div id="blogViewModalContent" class="d-none">
                                            <!-- Top Meta Banner -->
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 pb-3 border-bottom mb-4">
                                                <div>
                                                    <h3 class="fw-bold mb-2 text-dark" id="view_blog_title"></h3>
                                                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                                                        <span>
                                                            <i class="ri-folder-line text-primary me-1"></i>
                                                            <strong id="view_blog_category"></strong>
                                                        </span>
                                                        <span class="text-secondary">•</span>
                                                        <span>
                                                            <i class="ri-calendar-todo-line text-primary me-1"></i>
                                                            <span id="view_blog_date"></span>
                                                        </span>
                                                        <span class="text-secondary">•</span>
                                                        <span id="view_blog_type"></span>
                                                    </div>
                                                </div>
                                                <div id="view_blog_status"></div>
                                            </div>

                                            <!-- Featured Media -->
                                            <div id="view_media_wrapper" class="mb-4 d-none text-center">
                                                <img id="view_blog_image" src="" alt="Blog Featured Image" class="img-fluid rounded border shadow-sm d-none" style="max-height: 420px; width: 100%; object-fit: cover;">
                                                <div id="view_video_link" class="alert alert-info d-none text-start mb-0">
                                                    <i class="ri-video-line me-1"></i> <strong>Video URL:</strong> 
                                                    <a href="#" target="_blank" class="text-decoration-underline text-break" id="view_video_anchor"></a>
                                                </div>
                                            </div>

                                            <!-- Short Description Callout -->
                                            <div id="view_short_desc_wrapper" class="p-3 bg-light border-start border-4 border-primary rounded mb-4 d-none">
                                                <div class="fw-semibold text-primary small text-uppercase mb-1">
                                                    <i class="ri-chat-quote-line me-1"></i>Short Description
                                                </div>
                                                <div class="text-dark" id="view_blog_short_desc" style="font-size: 1.05rem; line-height: 1.6;"></div>
                                            </div>

                                            <!-- Full Description Card -->
                                            <div class="card border mb-4">
                                                <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between">
                                                    <span class="fw-bold text-dark">
                                                        <i class="ri-file-text-line text-primary me-2"></i>Full Description
                                                    </span>
                                                    <span class="badge bg-secondary-subtle text-secondary small">Rich Text Format</span>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div class="blog-desc-view" id="view_blog_desc"></div>
                                                </div>
                                            </div>

                                            <!-- Frequently Asked Questions -->
                                            <div id="view_faqs_wrapper" class="mb-3 d-none">
                                                <h6 class="fw-bold mb-3">
                                                    <i class="ri-questionnaire-line text-primary me-2"></i>Frequently Asked Questions (FAQs)
                                                </h6>
                                                <div id="view_faqs_container"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light py-2 px-3">
                                        <a href="#" id="view_blog_edit_btn" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                            <i class="ri-edit-box-line"></i> Edit Blog
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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

    <style>
        /* Blog Description Rich Text View Styles - Preserves all fonts, colors, and formatting */
        .blog-desc-view {
            font-family: inherit;
            font-size: 15px;
            line-height: 1.75;
            color: #2b303a;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .blog-desc-view h1,
        .blog-desc-view h2,
        .blog-desc-view h3,
        .blog-desc-view h4,
        .blog-desc-view h5,
        .blog-desc-view h6 {
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
            line-height: 1.35;
            color: #1a202c;
        }
        .blog-desc-view h1 { font-size: 1.85rem; }
        .blog-desc-view h2 { font-size: 1.55rem; }
        .blog-desc-view h3 { font-size: 1.35rem; }
        .blog-desc-view h4 { font-size: 1.15rem; }
        .blog-desc-view h5 { font-size: 1.05rem; }
        .blog-desc-view h6 { font-size: 0.95rem; }
        .blog-desc-view p {
            margin-bottom: 1rem;
            line-height: 1.75;
        }
        .blog-desc-view ul {
            list-style-type: disc !important;
            padding-left: 2rem !important;
            margin-bottom: 1rem;
        }
        .blog-desc-view ol {
            list-style-type: decimal !important;
            padding-left: 2rem !important;
            margin-bottom: 1rem;
        }
        .blog-desc-view ul ul {
            list-style-type: circle !important;
        }
        .blog-desc-view ul ul ul {
            list-style-type: square !important;
        }
        .blog-desc-view li {
            margin-bottom: 0.35rem;
            line-height: 1.65;
        }
        .blog-desc-view blockquote {
            margin: 1.25rem 0;
            padding: 0.85rem 1.25rem;
            border-left: 4px solid #116aef;
            background-color: #f8fafc;
            border-radius: 0 6px 6px 0;
            color: #4b5563;
            font-style: italic;
        }
        .blog-desc-view blockquote p:last-child {
            margin-bottom: 0;
        }
        .blog-desc-view table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 1.25rem 0;
            border: 1px solid #e2e8f0;
        }
        .blog-desc-view table th,
        .blog-desc-view table td {
            border: 1px solid #e2e8f0;
            padding: 0.65rem 0.85rem;
            vertical-align: top;
        }
        .blog-desc-view table th {
            background-color: #f1f5f9;
            font-weight: 600;
        }
        .blog-desc-view table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .blog-desc-view img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 6px;
            margin: 0.5rem 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        .blog-desc-view a {
            color: #116aef;
            text-decoration: underline;
            transition: color 0.2s ease;
        }
        .blog-desc-view a:hover {
            color: #0b4fc7;
        }
        .blog-desc-view code {
            background-color: #f1f5f9;
            color: #e11d48;
            padding: 0.15rem 0.35rem;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .blog-desc-view pre {
            background-color: #1e293b;
            color: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .blog-desc-view pre code {
            background-color: transparent;
            color: inherit;
            padding: 0;
        }
        .blog-desc-view hr {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 1.5rem 0;
        }
    </style>
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

        function openViewModal(blog_id) {
            $('#blogViewModal').modal('show');
            $('#blogViewModalLoader').removeClass('d-none');
            $('#blogViewModalContent').addClass('d-none');
            $('#blogViewModalError').addClass('d-none');

            let url = "{{ route('blog-detail', ['id' => ':id']) }}".replace(':id', blog_id);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        let d = response.data;
                        $('#view_blog_title').text(d.blog_title || '--');
                        $('#view_blog_date').text(d.blog_date || '--');
                        
                        // Category and Subcategory
                        let catText = d.category_name || '--';
                        if (d.subcategory_name) {
                            catText += ' &rsaquo; ' + d.subcategory_name;
                        }
                        $('#view_blog_category').html(catText);

                        // Status
                        if (d.blog_status == '1') {
                            $('#view_blog_status').html('<span class="badge bg-success">Active</span>');
                        } else {
                            $('#view_blog_status').html('<span class="badge bg-danger">Inactive</span>');
                        }

                        // Type
                        let typeBadge = (d.blog_type === 'video') 
                            ? '<span class="badge bg-info-subtle text-info"><i class="ri-video-line me-1"></i>Video</span>'
                            : '<span class="badge bg-primary-subtle text-primary"><i class="ri-image-line me-1"></i>Image</span>';
                        $('#view_blog_type').html(typeBadge);

                        // Media Preview
                        if (d.blog_type === 'video' && d.blog_video_url) {
                            $('#view_media_wrapper').removeClass('d-none');
                            $('#view_blog_image').addClass('d-none');
                            $('#view_video_link').removeClass('d-none');
                            $('#view_video_anchor').attr('href', d.blog_video_url).text(d.blog_video_url);
                        } else if (d.blog_image_url) {
                            $('#view_media_wrapper').removeClass('d-none');
                            $('#view_video_link').addClass('d-none');
                            $('#view_blog_image').removeClass('d-none').attr('src', d.blog_image_url);
                        } else {
                            $('#view_media_wrapper').addClass('d-none');
                        }

                        // Short Description
                        if (d.blog_short_desc && d.blog_short_desc.trim() !== '') {
                            $('#view_short_desc_wrapper').removeClass('d-none');
                            $('#view_blog_short_desc').text(d.blog_short_desc);
                        } else {
                            $('#view_short_desc_wrapper').addClass('d-none');
                        }

                        // Description (Full HTML with all styles, fonts, and colors preserved)
                        if (d.blog_desc && d.blog_desc.trim() !== '') {
                            $('#view_blog_desc').html(d.blog_desc);
                        } else {
                            $('#view_blog_desc').html('<p class="text-muted fst-italic">No description provided.</p>');
                        }

                        // FAQs
                        if (d.faqs && d.faqs.length > 0) {
                            $('#view_faqs_wrapper').removeClass('d-none');
                            let faqsHtml = '';
                            d.faqs.forEach(function(faq, idx) {
                                let q = $('<div>').text(faq.question || '').html();
                                let a = $('<div>').text(faq.answer || '').html();
                                faqsHtml += `
                                    <div class="card border mb-3 shadow-none bg-light-subtle">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">Q${idx + 1}</span>
                                                <h6 class="fw-bold mb-0 text-dark pt-1">${q}</h6>
                                            </div>
                                            <div class="ps-4 text-secondary" style="white-space: pre-line; line-height: 1.6;">${a}</div>
                                        </div>
                                    </div>
                                `;
                            });
                            $('#view_faqs_container').html(faqsHtml);
                        } else {
                            $('#view_faqs_wrapper').addClass('d-none');
                        }

                        // Edit Button Link
                        if (d.edit_url) {
                            $('#view_blog_edit_btn').attr('href', d.edit_url).removeClass('d-none');
                        } else {
                            $('#view_blog_edit_btn').addClass('d-none');
                        }

                        $('#blogViewModalLoader').addClass('d-none');
                        $('#blogViewModalContent').removeClass('d-none');
                    } else {
                        $('#blogViewModalLoader').addClass('d-none');
                        $('#blogViewModalError').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#blogViewModalLoader').addClass('d-none');
                    $('#blogViewModalError').removeClass('d-none');
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
