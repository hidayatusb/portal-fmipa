<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Daftar Judul Skripsi
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Rekap judul skripsi mahasiswa beserta dosen pembimbing.
                </p>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        <div class="grid w-full space-y-5">
            <div class="kt-card">
                <div class="kt-card-header min-h-16 flex-wrap gap-3">
                    <input type="text" placeholder="Cari NIM, nama, judul, pembimbing..."
                        class="kt-input sm:w-72" wire:model.live.debounce.300ms="search" />
                    <select class="kt-select w-28" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                @if ($items->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-questionnaire-tablet text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">
                            {{ $search !== '' ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data judul skripsi.' }}
                        </p>
                    </div>
                @else
                    <div class="kt-card-table">
                        <div class="kt-table-wrapper kt-scrollable">
                            <table class="kt-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="w-16">No</th>
                                        <th scope="col" class="min-w-[120px]">NIM</th>
                                        <th scope="col" class="min-w-[160px]">Nama Mahasiswa</th>
                                        <th scope="col" class="min-w-[280px]">Judul</th>
                                        <th scope="col" class="min-w-[200px]">Pembimbing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr wire:key="judul-skripsi-{{ $item->id }}">
                                            <td>{{ $items->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->nim }}</td>
                                            <td>{{ $item->nama_mahasiswa }}</td>
                                            <td>{{ $item->judul }}</td>
                                            <td>
                                                <div class="flex flex-col gap-0.5">
                                                    <span>1. {{ $item->pembimbing1 }}</span>
                                                    <span>2. {{ $item->pembimbing2 ?: '-' }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-4">
                        <p class="text-sm text-secondary-foreground">
                            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
                            dari {{ $items->total() }} data
                        </p>
                        <div>
                            {{ $items->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
