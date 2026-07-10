<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Review Akun
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Setujui atau tolak pendaftaran dosen dan mahasiswa
                </p>
            </div>
            @if ($pendingCount > 0)
                <span class="kt-badge kt-badge-warning kt-badge-outline">
                    {{ $pendingCount }} menunggu review
                </span>
            @endif
        </div>
    </div>

    <div class="kt-container-fixed">
        @if (session('success'))
            <div class="kt-alert kt-alert-success mb-5 flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid w-full space-y-5">
            <div class="kt-card">
                <div class="kt-card-header min-h-16 flex-wrap gap-3">
                    <input type="text" placeholder="Cari akun..." class="kt-input sm:w-48"
                        data-kt-datatable-search="#kt_datatable_user_approvals" />
                    <select class="kt-select sm:w-48" wire:model.live="status">
                        <option value="all">Semua Status</option>
                        <option value="pending">Menunggu Review</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div id="kt_datatable_user_approvals" class="kt-card-table" data-kt-datatable="true"
                    data-kt-datatable-page-size="10" data-kt-datatable-state-save="true" wire:key="user-approvals-datatable">
                    <div class="kt-table-wrapper kt-scrollable">
                        <table class="kt-table" data-kt-datatable-table="true">
                            <thead>
                                <tr>
                                    <th scope="col" class="min-w-[160px]" data-kt-datatable-column="name">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Nama</span>
                                            <span class="kt-table-col-sort"></span>
                                        </span>
                                    </th>
                                    <th scope="col" class="min-w-[120px]" data-kt-datatable-column="username">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Username</span>
                                            <span class="kt-table-col-sort"></span>
                                        </span>
                                    </th>
                                    <th scope="col" class="min-w-[180px]" data-kt-datatable-column="email">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Email</span>
                                            <span class="kt-table-col-sort"></span>
                                        </span>
                                    </th>
                                    <th scope="col" class="w-28" data-kt-datatable-column="role">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Role</span>
                                            <span class="kt-table-col-sort"></span>
                                        </span>
                                    </th>
                                    <th scope="col" class="w-36" data-kt-datatable-column="status">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Status</span>
                                            <span class="kt-table-col-sort"></span>
                                        </span>
                                    </th>
                                    <th scope="col" class="w-40" data-kt-datatable-column="registeredAt">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Tanggal Daftar</span>
                                            <span class="kt-table-col-sort"></span>
                                        </span>
                                    </th>
                                    <th scope="col" class="w-28" data-kt-datatable-column="actions"
                                        data-kt-datatable-column-sort="false">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Aksi</span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr wire:key="user-row-{{ $user->id }}">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $user->role->label() }}</span>
                                        </td>
                                        <td>
                                            <span class="kt-badge kt-badge-sm {{ $user->approval_status->badgeClass() }}">
                                                {{ $user->approval_status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                        <td class="text-end">
                                            @if ($user->isPendingApproval())
                                                <span class="inline-flex gap-2">
                                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                                        wire:click="approve({{ $user->id }})"
                                                        wire:confirm="Setujui akun {{ $user->name }}?"
                                                        aria-label="Setujui">
                                                        <i class="ki-filled ki-check text-xs"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                        wire:click="reject({{ $user->id }})"
                                                        wire:confirm="Tolak akun {{ $user->name }}?"
                                                        aria-label="Tolak">
                                                        <i class="ki-filled ki-cross text-xs"></i>
                                                    </button>
                                                </span>
                                            @else
                                                <span class="text-xs text-muted-foreground">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-sm text-secondary-foreground py-10">
                                            Tidak ada akun yang cocok dengan filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="kt-datatable-toolbar">
                        <div class="kt-datatable-length">
                            Show<select class="kt-select kt-select-sm w-16" name="perpage"
                                data-kt-datatable-size="true"></select>per page
                        </div>
                        <div class="kt-datatable-info">
                            <span data-kt-datatable-info="true"></span>
                            <div class="kt-datatable-pagination" data-kt-datatable-pagination="true"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        
          
    </div>
</div>
