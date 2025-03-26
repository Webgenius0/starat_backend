<!-- BEGIN: Vendor JS-->
<script src="{{ asset('backend/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('backend/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('backend/app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('backend/app-assets/js/core/app.js') }}"></script>
<!-- END: Theme JS-->

{{-- Toaster --}}
<script src="{{ asset('vendor/toaster/toastr.min.js') }}"></script>

<!-- BEGIN: Page JS-->
<script src="{{ asset('backend/app-assets/js/scripts/pages/page-auth-login.js') }}"></script>
<!-- END: Page JS-->

<script>
    $(document).ready(function() {
        toastr.options = {
            'closeButton': true,
            'debug': true,
            'newestOnTop': true,
            'progressBar': false,
            'positionClass': 'toast-top-center',
            'preventDuplicates': true,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '5000',
            'extendedTimeOut': '1000',
            'showEasing': 'linear',
            'hideEasing': 'linear',
            'showMethod': 'slideDown',
            'hideMethod': 'slideUp',
            'hover': false,
        };

        @if (Session::has('t-success'))
            toastr.success("{{ session('t-success') }}");
        @endif

        @if (Session::has('status'))
            toastr.success("{{ session('status') }}");
        @endif

        @if (Session::has('t-error'))
            toastr.error("{{ session('t-error') }}");
        @endif

        @if (Session::has('t-info'))
            toastr.info("{{ session('t-info') }}");
        @endif

        @if (Session::has('t-warning'))
            toastr.warning("{{ session('t-warning') }}");
        @endif
    });

    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>
@stack('script')
