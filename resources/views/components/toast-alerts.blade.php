<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = () => document.documentElement.classList.contains('dark');

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: isDarkMode() ? '#0f172a' : '#ffffff',
            color: isDarkMode() ? '#f8fafc' : '#0f172a',
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: @json(session('success')) });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: @json(session('error')) });
        @endif

        @if(session('info'))
            Toast.fire({ icon: 'info', title: @json(session('info')) });
        @endif

        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: @json(session('warning')) });
        @endif
    });
</script>