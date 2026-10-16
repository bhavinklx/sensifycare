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
            <li class="breadcrumb-item">
                <a href="{{ route("faq-list") }}">FAQs</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Add FAQ
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <form id="faqFrm" method="post" action="{{ route("faq-insert") }}">
        {{ csrf_field() }}
        <div class="app-body">
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Add FAQ</h5>
                        </div>
                        <div class="card-body">
                            <!-- Category & Status selection -->
                            <div class="row gx-3">
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="fcategory_id">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" id="fcategory_id" name="fcategory_id">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->fcategory_id }}">{{ $category->fcategory_title }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="msg_fcategory_id"></div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-lg-6 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="faq_status">Status</label>
                                        <select class="form-select" id="faq_status" name="faq_status">
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" id="msg_faq_status"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- FAQ Repeater Container (Two 6-column cards side by side in row) -->
                            <div id="faq_repeater_container">
                                <div class="row gx-3" id="faq_cards_row">
                                    <!-- Initial FAQ Item #1 (col-6, green Add '+' icon only) -->
                                    <div class="col-xxl-6 col-lg-6 col-sm-12 mb-3 faq-item-col" data-index="0">
                                        <div class="card border h-100 m-0">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-end align-items-center mb-3">
                                                    <button type="button" class="btn btn-success btn-sm add-faq-btn" title="Add FAQ">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Question <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control faq-question-input" name="faqs[0][question]" placeholder="Enter Question">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Answer <span class="text-danger">*</span></label>
                                                    <textarea class="form-control faq-answer-input" name="faqs[0][answer]" rows="4" placeholder="Enter Answer"></textarea>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row g-3 mt-3">
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('faq-list') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary" id="btn_submit">
                                            Save
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
        let faqIndex = 0;

        $(document).on('click', '.add-faq-btn', function() {
            faqIndex++;
            let html = `
                <div class="col-xxl-6 col-lg-6 col-sm-12 mb-3 faq-item-col" data-index="${faqIndex}">
                    <div class="card border h-100 m-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                <button type="button" class="btn btn-danger btn-sm remove-faq-btn" title="Remove FAQ">
                                    <i class="ri-subtract-line"></i>
                                </button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Question <span class="text-danger">*</span></label>
                                <input type="text" class="form-control faq-question-input" name="faqs[${faqIndex}][question]" placeholder="Enter Question">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Answer <span class="text-danger">*</span></label>
                                <textarea class="form-control faq-answer-input" name="faqs[${faqIndex}][answer]" rows="4" placeholder="Enter Answer"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#faq_cards_row').append(html);
            $('#faq_cards_row .faq-item-col:last .faq-question-input').focus();
        });

        $(document).on('click', '.remove-faq-btn', function() {
            let col = $(this).closest('.faq-item-col');
            col.fadeOut(200, function() {
                $(this).remove();
            });
        });

        $('#faqFrm').submit(function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            let categoryId = $('#fcategory_id').val();
            let hasError = false;

            if (!categoryId) {
                $('#fcategory_id').addClass('is-invalid');
                $('#msg_fcategory_id').html('Please select a category.');
                hasError = true;
            }

            // Client-side row check
            let validRows = 0;
            $('#faq_cards_row .faq-item-col').each(function() {
                let qInput = $(this).find('.faq-question-input');
                let aInput = $(this).find('.faq-answer-input');
                let qVal = $.trim(qInput.val());
                let aVal = $.trim(aInput.val());

                if (qVal && !aVal) {
                    aInput.addClass('is-invalid');
                    aInput.next('.invalid-feedback').html('Please enter an answer for this question.').show();
                    hasError = true;
                } else if (!qVal && aVal) {
                    qInput.addClass('is-invalid');
                    qInput.next('.invalid-feedback').html('Please enter a question for this answer.').show();
                    hasError = true;
                } else if (qVal && aVal) {
                    validRows++;
                }
            });

            if (validRows === 0 && !hasError) {
                let firstQ = $('#faq_cards_row .faq-item-col:first .faq-question-input');
                firstQ.addClass('is-invalid');
                firstQ.next('.invalid-feedback').html('Please enter at least one Question and Answer.').show();
                hasError = true;
            }

            if (hasError) {
                return false;
            }

            $('#loading-wrapper').fadeIn(200);
            let formData = new FormData(this);

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
                    $('#loading-wrapper').fadeOut(200);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.fcategory_id) {
                            $('#fcategory_id').addClass('is-invalid');
                            $('#msg_fcategory_id').html(errors.fcategory_id[0]);
                        }
                        if (errors.faqs) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errors.faqs[0]
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving FAQs. Please try again.'
                        });
                    }
                }
            });
        });
    </script>
@endsection
