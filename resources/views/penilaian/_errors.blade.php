@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show card-custom border-danger mb-4" role="alert">
        <strong>Gagal menyimpan!</strong>
        <ul class="mb-0 mt-1 fs-7">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
