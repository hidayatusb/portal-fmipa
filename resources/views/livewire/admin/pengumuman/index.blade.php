<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Pengumuman
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Kelola pengumuman dengan teks dan gambar untuk seluruh pengguna portal
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.pengumuman.create') }}" class="kt-btn kt-btn-primary" wire:navigate>
                    <i class="ki-filled ki-plus-squared"></i>
                    Tambah Pengumuman
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        @if (session('success'))
            <div class="kt-alert kt-alert-success mb-5 flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Daftar Pengumuman</h3>
            </div>

            @if ($announcements->isEmpty())
                <div class="kt-card-content py-10 text-center text-sm text-secondary-foreground">
                    Belum ada pengumuman. Klik "Tambah Pengumuman" untuk membuat yang pertama.
                </div>
            @else
                <div class="kt-card-table">
                    <div class="kt-table-wrapper kt-scrollable">
                        <table class="kt-table">
                            <thead>
                                <tr>
                                    <th class="min-w-[80px]">Gambar</th>
                                    <th class="min-w-[220px]">Judul</th>
                                    <th class="w-24">Tipe</th>
                                    <th class="min-w-[280px]">Isi</th>
                                    <th class="w-32">Status</th>
                                    <th class="w-40">Tanggal</th>
                                    <th class="w-48">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($announcements as $announcement)
                                    <tr wire:key="announcement-{{ $announcement->id }}">
                                        <td>
                                            @if ($announcement->hasImage())
                                                <img src="{{ $announcement->imageUrl() }}"
                                                    alt="{{ $announcement->title }}"
                                                    class="size-14 rounded-lg border border-border object-cover" />
                                            @else
                                                <span
                                                    class="flex size-14 items-center justify-center rounded-lg border border-dashed border-border text-muted-foreground">
                                                    <i class="ki-filled ki-picture"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-medium text-mono">{{ $announcement->title }}</div>
                                            @if ($announcement->author)
                                                <div class="text-xs text-secondary-foreground">
                                                    oleh {{ $announcement->author->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="kt-badge kt-badge-outline">
                                                {{ $announcement->content_type->label() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($announcement->isUrlContent())
                                                <a href="{{ $announcement->body }}" target="_blank" rel="noopener"
                                                    class="line-clamp-2 text-sm text-primary break-all">
                                                    {{ $announcement->body }}
                                                </a>
                                            @else
                                                <p class="line-clamp-2 text-sm text-secondary-foreground">
                                                    {{ $announcement->body }}
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($announcement->is_published)
                                                <span class="kt-badge kt-badge-success kt-badge-outline">Publik</span>
                                            @else
                                                <span class="kt-badge kt-badge-warning kt-badge-outline">Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-sm text-secondary-foreground">
                                            {{ optional($announcement->published_at ?? $announcement->created_at)->timezone(config('app.timezone'))->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <a href="{{ route('admin.pengumuman.edit', $announcement) }}"
                                                    class="kt-btn kt-btn-sm kt-btn-outline" wire:navigate>
                                                    Edit
                                                </a>
                                                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                                    wire:click="togglePublish({{ $announcement->id }})">
                                                    {{ $announcement->is_published ? 'Sembunyikan' : 'Publikasikan' }}
                                                </button>
                                                <button type="button"
                                                    class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                    wire:click="delete({{ $announcement->id }})"
                                                    wire:confirm="Hapus pengumuman ini?">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
