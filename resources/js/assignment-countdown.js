function defineAssignmentCountdown() {
    Alpine.data('assignmentCountdown', (dueAtMs, createdAtMs) => ({
        dueAt: dueAtMs,
        createdAt: createdAtMs,
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        remainingLabel: '',
        progress: 0,
        isOverdue: false,
        timer: null,

        init() {
            this.tick();
            this.timer = setInterval(() => this.tick(), 1000);
        },

        destroy() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        tick() {
            const now = Date.now();
            this.isOverdue = this.dueAt <= now;

            if (this.isOverdue) {
                this.remainingLabel = 'Batas waktu sudah berakhir';
                this.days = 0;
                this.hours = 0;
                this.minutes = 0;
                this.seconds = 0;
                this.progress = 100;

                return;
            }

            let total = Math.floor((this.dueAt - now) / 1000);
            this.days = Math.floor(total / 86400);
            total %= 86400;
            this.hours = Math.floor(total / 3600);
            total %= 3600;
            this.minutes = Math.floor(total / 60);
            this.seconds = total % 60;

            if (this.days > 0) {
                this.remainingLabel = `${this.days} hari ${this.hours} jam ${this.minutes} menit ${this.seconds} detik lagi`;
            } else if (this.hours > 0) {
                this.remainingLabel = `${this.hours} jam ${this.minutes} menit ${this.seconds} detik lagi`;
            } else if (this.minutes > 0) {
                this.remainingLabel = `${this.minutes} menit ${this.seconds} detik lagi`;
            } else {
                this.remainingLabel = `${this.seconds} detik lagi`;
            }

            const totalMs = Math.max(1, this.dueAt - this.createdAt);
            const elapsed = now - this.createdAt;
            this.progress = Math.min(100, Math.max(0, Math.round((elapsed / totalMs) * 100)));
        },
    }));
}

export function registerAssignmentCountdown() {
    if (window.Alpine) {
        defineAssignmentCountdown();
    }

    document.addEventListener('alpine:init', defineAssignmentCountdown);
}
