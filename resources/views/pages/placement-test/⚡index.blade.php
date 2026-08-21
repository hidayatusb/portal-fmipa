<?php

use App\Models\PlacementTestResult;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::login')] class extends Component {
    #[Validate('required', message: 'NIM wajib diisi!')]
    public string $nim = '';

    public ?PlacementTestResult $result = null;

    public bool $searched = false;

    public function lihatHasil(): void
    {
        $this->validate();

        $this->result = PlacementTestResult::find(trim($this->nim));
        $this->searched = true;
    }
};
?>

<div>
    <form wire:submit.prevent="lihatHasil" class="flex flex-col gap-5 p-10">
        <div class="mb-2.5 text-center">
            <div class="mb-4 flex justify-center">
                <img
                    style="height: 80px;"
                    class="max-h-[28px] max-w-none rounded-xl dark:hidden"
                    src="{{ asset('assets/media/app/portal.png') }}"
                    alt="Portal FMIPA"
                />
                <img
                    class="hidden min-h-[28px] max-w-none dark:inline-block"
                    src="{{ asset('assets/media/app/portal.png') }}"
                    alt="Portal FMIPA"
                />
            </div>
            <h3 class="mb-2.5 text-lg font-medium leading-none text-mono">
                Pengumuman Placement Test
            </h3>
            <p class="text-sm text-secondary-foreground">
                Masukkan NIM untuk melihat hasil
            </p>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground">NIM</label>
            <input
                type="text"
                wire:model="nim"
                placeholder="Masukkan NIM"
                class="w-full rounded-md border border-input bg-background px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20"
            />
            @error('nim')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex w-full items-center justify-center rounded-md bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="lihatHasil">Lihat Hasil</span>
            <span wire:loading wire:target="lihatHasil">Mencari...</span>
        </button>

        @if ($searched)
            @if ($result)
                <div class="mt-1 rounded-lg border border-border bg-muted/40 p-5">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-mono">Hasil Placement Test</p>
                       
                    </div>

                    <div class="flex flex-col gap-3 text-sm">
                        <div class="flex items-start justify-between gap-4 border-b border-border pb-2.5">
                            <span class="shrink-0 text-secondary-foreground">NIM</span>
                            <span class="text-right font-medium text-mono">{{ $result->nim }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-border pb-2.5">
                            <span class="shrink-0 text-secondary-foreground">Nama</span>
                            <span class="text-right font-medium text-mono">{{ $result->nama }}</span>
                        </div>
                        @if ($result->isLulus())
                            <div class="flex items-start justify-between gap-4 border-b border-border pb-2.5">
                                <span class="shrink-0 text-secondary-foreground">Nilai</span>
                                <span class="text-right font-medium text-mono">{{ $result->nilai }}</span>
                            </div>
                        @endif
                        <div class="flex items-start justify-between gap-4 border-b border-border pb-2.5">
                            <span class="shrink-0 text-secondary-foreground">Hasil</span>
                            <span @class([
                                'text-right font-medium',
                                'text-green-600 dark:text-green-400' => $result->isLulus(),
                                'text-red-600 dark:text-red-400' => ! $result->isLulus(),
                            ])>
                                {{ $result->hasil }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-4 pt-0.5">
                            <span class="shrink-0 text-secondary-foreground">Sertifikat</span>
                            @if ($result->keterangan)
                                <a
                                    href="{{ $result->keterangan }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium text-foreground transition hover:bg-muted"
                                >
                                    Unduh Sertifikat
                                </a>
                            @else
                                <span class="text-secondary-foreground">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-1 rounded-lg border border-amber-200 bg-amber-50 p-5 text-center dark:border-amber-900/50 dark:bg-amber-950/30">
                    <p class="mb-1 text-sm font-medium text-mono">Data tidak ditemukan</p>
                    <p class="text-sm text-secondary-foreground">
                        Hasil untuk NIM <span class="font-medium text-foreground">{{ $nim }}</span> tidak ditemukan.
                        Periksa kembali NIM Anda.
                    </p>
                </div>
            @endif
        @endif
    </form>

   
</div>
