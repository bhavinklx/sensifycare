<script src="{{ url('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('assets/js/moment.min.js') }}"></script>

<!-- Overlay Scroll JS -->
<script src="{{ url('assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ url('assets/vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

<!-- Dropzone JS -->
<script src="{{ url('assets/vendor/dropzone/dropzone.min.js') }}"></script>

<!-- Quill Editor JS -->
<script src="{{ url('assets/vendor/quill/quill.min.js') }}"></script>
<script src="{{ url('assets/vendor/quill/custom.js') }}"></script>

<!-- Data Tables -->
<script src="{{ url('assets/vendor/datatables/dataTables.min.js') }}"></script>
<script src="{{ url('assets/vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ url('assets/vendor/datatables/custom/custom-datatables.js') }}"></script>

<!-- Custom JS files -->
<script src="{{ url('assets/js/custom.js') }}"></script>

<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(Session::get('failedMsg') != "")
    Swal.fire({
        toast: true,
        position: 'top-end',      // top-right corner
        icon: 'error',          // success, error, warning, info
        title: "{{ Session::get('failedMsg') }}",          // message text
        showConfirmButton: false, // no OK button
        timer: 3500,              // auto close after 3.5 seconds
        timerProgressBar: true,
        padding: '0.5em 1em',      // smaller padding
    });
    @elseif(Session::get('successMsg') != "")
    Swal.fire({
        toast: true,
        position: 'top-end',      // top-right corner
        icon: 'success',          // success, error, warning, info
        title: "{{ Session::get('successMsg') }}",          // message text
        showConfirmButton: false, // no OK button
        timer: 3500,              // auto close after 3.5 seconds
        timerProgressBar: true,
        padding: '0.5em 1em',      // smaller padding
    });
    @endif
</script>