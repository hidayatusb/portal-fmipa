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
                </div>

                @if ($items->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-questionnaire-tablet text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">
                            {{ $search !== '' ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data judul skripsi.' }}
                        </p>
                    </div>
                @else
                    <div id="kt_datatable_daftar_judul_skripsi" class="kt-card-table" data-kt-datatable="true"
                        data-kt-datatable-page-size="50" data-kt-datatable-state-save="false"
                        wire:key="daftar-judul-skripsi-{{ md5($search) }}-{{ $items->count() }}">
                        <div class="kt-table-wrapper kt-scrollable">
                            <table class="kt-table" data-kt-datatable-table="true">
                                <thead>
                                    <tr>
                                        <th scope="col" class="w-16" data-kt-datatable-column="no"
                                            data-kt-datatable-column-sort="false">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">No</span>
                                            </span>
                                        </th>
                                        <th scope="col" class="min-w-[120px]" data-kt-datatable-column="nim">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">NIM</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th scope="col" class="min-w-[160px]" data-kt-datatable-column="nama">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">Nama Mahasiswa</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th scope="col" class="min-w-[280px]" data-kt-datatable-column="judul">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">Judul</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th scope="col" class="min-w-[200px]" data-kt-datatable-column="pembimbing">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">Pembimbing</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr wire:key="judul-skripsi-{{ $item->id }}">
                                            <td>{{ $index + 1 }}</td>
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
                @endif
            </div>
        </div>
    </div>
</div>
