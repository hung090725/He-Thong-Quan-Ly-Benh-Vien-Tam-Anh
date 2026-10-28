@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản Người dùng')

@section('content')
<x-page-header title="Quản lý Người dùng" subtitle="Danh sách tất cả tài khoản trong hệ thống phân quyền 4 vai trò">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-plus-fill"></i> Thêm tài khoản mới
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Filter & Search Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Tìm theo tên, email hoặc số điện thoại...">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <select name="role" class="form-select">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    <option value="receptionist" {{ request('role') == 'receptionist' ? 'selected' : '' }}>Lễ tân (Receptionist)</option>
                    <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Bác sĩ (Doctor)</option>
                    <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Bệnh nhân (Patient)</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Họ và tên</th>
                        <th>Email liên hệ</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò (Role)</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-3" style="width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-3 text-muted fw-bold">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; border: 1px solid #e2e8f0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                        @if($user->id === Auth::id())
                                            <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">(Tài khoản của bạn)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code>{{ $user->email }}</code>
                            </td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $user->role_badge_class }} rounded-pill px-2 py-1">
                                    {{ $user->role_name }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                        <i class="bi bi-lock-fill me-1"></i> Đã khóa
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @if($user->id !== Auth::id())
                                        <button type="button" class="btn btn-outline-danger" title="Xóa tài khoản" onclick="if(confirm('Bạn có chắc chắn muốn xóa tài khoản {{ addslashes(e($user->name)) }}?')) document.getElementById('delete-user-{{ $user->id }}').submit();">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form id="delete-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy người dùng nào phù hợp với bộ lọc tìm kiếm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
