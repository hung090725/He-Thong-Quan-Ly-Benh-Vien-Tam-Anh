@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 border-start border-success border-4 rounded-3 py-3 px-3 bg-success bg-opacity-10" role="alert">
        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 34px; height: 34px;">
            <i class="bi bi-check-lg fs-5"></i>
        </div>
        <div class="flex-grow-1 text-success-emphasis fw-medium">
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 border-start border-danger border-4 rounded-3 py-3 px-3 bg-danger bg-opacity-10" role="alert">
        <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 34px; height: 34px;">
            <i class="bi bi-exclamation-triangle-fill fs-6"></i>
        </div>
        <div class="flex-grow-1 text-danger-emphasis fw-medium">
            {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 border-start border-warning border-4 rounded-3 py-3 px-3 bg-warning bg-opacity-10" role="alert">
        <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 34px; height: 34px;">
            <i class="bi bi-exclamation-circle-fill fs-6"></i>
        </div>
        <div class="flex-grow-1 text-warning-emphasis fw-medium">
            {{ session('warning') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 border-start border-info border-4 rounded-3 py-3 px-3 bg-info bg-opacity-10" role="alert">
        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 34px; height: 34px;">
            <i class="bi bi-info-circle-fill fs-6"></i>
        </div>
        <div class="flex-grow-1 text-info-emphasis fw-medium">
            {{ session('info') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0 border-start border-danger border-4 rounded-3 py-3 px-3 bg-danger bg-opacity-10" role="alert">
        <div class="d-flex align-items-center mb-2">
            <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 30px; height: 30px;">
                <i class="bi bi-x-octagon-fill fs-6"></i>
            </div>
            <strong class="flex-grow-1 text-danger-emphasis">Đã có lỗi xảy ra! Vui lòng kiểm tra lại:</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <ul class="mb-0 ps-4 text-danger-emphasis">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
