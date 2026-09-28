@push('scripts')
<style>[x-cloak] { display: none !important; }</style>
<script>
    // Validasi awal di browser: hanya PDF & batas ukuran file (tetap divalidasi ulang di server)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.file-pdf').forEach(function (input) {
            const error = input.parentElement.querySelector('.file-error');
            input.addEventListener('change', function () {
                const file = input.files[0];
                let pesan = '';
                if (file) {
                    const maxKb = parseInt(input.dataset.maxKb, 10);
                    if (!/\.pdf$/i.test(file.name) || (file.type && file.type !== 'application/pdf')) {
                        pesan = 'File harus berformat PDF.';
                    } else if (file.size > maxKb * 1024) {
                        pesan = 'Ukuran file ' + (file.size / 1024 / 1024).toFixed(2) + ' MB melebihi batas maksimal ' + (maxKb / 1024) + ' MB.';
                    }
                }
                if (pesan) {
                    input.value = '';
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
                if (error) error.textContent = pesan;
            });
        });
    });
</script>
@endpush
