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
                <div class="kt-card-header min-h-16">
                    <input type="text" placeholder="Cari NIM, nama, judul, pembimbing..."
                        class="kt-input sm:w-72" wire:model.live.debounce.300ms="search" />
                </div>
                <div id="kt_datatable_daftar_judul_skripsi" class="kt-card-table">
                    <div class="kt-table-wrapper kt-scrollable">
                        <table class="kt-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="w-16">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">No</span>
                                        </span>
                                    </th>
                                    <th scope="col" class="min-w-[120px]">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">NIM</span>
                                        </span>
                                    </th>
                                    <th scope="col" class="min-w-[160px]">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Nama Mahasiswa</span>
                                        </span>
                                    </th>
                                    <th scope="col" class="min-w-[280px]">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Judul</span>
                                        </span>
                                    </th>
                                    <th scope="col" class="min-w-[200px]">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">Pembimbing</span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr wire:key="judul-skripsi-{{ $item->id }}">
                                        <td>{{ $items->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->nim }}</td>
                                        <td>{{ $item->nama_mahasiswa }}</td>
                                        <td>{{ $item->judul }}</td>
                                        <td>
                                            - {{ $item->pembimbing1 }}
                                            <br>
                                            - {{ $item->pembimbing2 ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-sm text-secondary-foreground">
                                            {{ $search !== '' ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data judul skripsi.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($items->total() > 0)
                        <div class="kt-datatable-toolbar">
                            <div class="kt-datatable-length">
                                Show
                                <select class="kt-select kt-select-sm w-16" wire:model.live="perPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                per page
                            </div>
                            <div class="kt-datatable-info">
                                <span>
                                    {{ $items->firstItem() }}-{{ $items->lastItem() }} of {{ $items->total() }}
                                </span>
                                {{ $items->onEachSide(1)->links('livewire.skripsi.partials.pagination') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
