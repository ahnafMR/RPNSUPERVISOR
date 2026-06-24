<div class="card mt-3">
    <div class="card-header"><h3 class="card-title">Timeline Progres Temuan</h3></div>
    <div class="card-body">
        <div class="timeline">
            <div class="time-label"><span class="bg-info">{{ $temuan->created_at->format('d M Y') }}</span></div>
            <div>
                <i class="fas fa-exclamation-circle bg-warning"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> {{ $temuan->created_at->format('H:i') }}</span>
                    <h3 class="timeline-header">Temuan Dibuat</h3>
                    <div class="timeline-body">{{ $temuan->judul_temuan }} - Status: {{ $temuan->statusLabel() }}</div>
                </div>
            </div>
            @if($temuan->prosesTemuan)
            <div>
                <i class="fas fa-cogs bg-primary"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> {{ $temuan->prosesTemuan->created_at->format('H:i') }}</span>
                    <h3 class="timeline-header">Proses Perbaikan</h3>
                    <div class="timeline-body">PIC: {{ $temuan->prosesTemuan->pic }}<br>{{ $temuan->prosesTemuan->tindakan }}</div>
                </div>
            </div>
            @endif
            @if($temuan->hasilTemuan)
            <div>
                <i class="fas fa-check bg-success"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> {{ $temuan->hasilTemuan->created_at->format('H:i') }}</span>
                    <h3 class="timeline-header">Selesai</h3>
                    <div class="timeline-body">{{ $temuan->hasilTemuan->hasil_perbaikan }}</div>
                </div>
            </div>
            @endif
            <div><i class="fas fa-clock bg-gray"></i></div>
        </div>
    </div>
</div>
