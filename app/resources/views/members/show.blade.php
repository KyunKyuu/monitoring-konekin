@extends('layouts.dashboard', ['title' => $member->name])

@section('dashboard-content')
    <section class="page-header">
        <div>
            <div class="mono-eyebrow">PROFILE MEMBER</div>
            <h1 class="page-title">{{ $member->name }}</h1>
            <p class="page-copy">{{ $member->code }} · {{ $member->target_role ?: 'Belum ada target pembinaan' }}</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('members.edit', $member) }}" class="button button-secondary">Edit</a>
            @if (auth()->user()?->hasRole('super_admin'))
                <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Hapus member ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-ghost">Hapus</button>
                </form>
            @endif
        </div>
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">TARGET</div>
                    <h2>Profil pembinaan</h2>
                </div>
            </div>
            <div class="detail-list">
                <div><span>Fungsi</span><strong>{{ $member->target_function ?: '-' }}</strong></div>
                <div><span>Role</span><strong>{{ $member->target_role ?: '-' }}</strong></div>
                <div><span>Prioritas</span><strong>{{ $member->note_priority }}</strong></div>
                <div><span>Status</span><strong>{{ $member->status }}</strong></div>
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">KEGIATAN</div>
                    <h2>Riwayat terhubung</h2>
                </div>
            </div>
            <div class="signal-list">
                @forelse ($member->activities as $activity)
                    <div class="signal-row">
                        <span class="tag-chip">{{ $activity->category }}</span>
                        <p>{{ $activity->title }} · {{ $activity->scheduled_at?->format('d M Y H:i') }}</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada kegiatan.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="content-grid">
        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">TARGET AKTIF</div>
                    <h2>Arah pembinaan</h2>
                </div>
            </div>
            <div class="priority-list">
                @forelse ($member->developmentTargets as $target)
                    <div class="priority-card">
                        <div class="priority-head">
                            <strong>{{ $target->role_name }}</strong>
                            <span>{{ $target->function_name }} · {{ $target->priority }} · {{ $target->status }}</span>
                        </div>
                        <p>{{ $target->next_action ?: ($target->goal ?: 'Belum ada arahan lanjutan.') }}</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada target pembinaan.</p>
                @endforelse
            </div>
        </article>

        <article class="feature-card">
            <div class="section-heading">
                <div>
                    <div class="mono-eyebrow">PROGRESS</div>
                    <h2>Update perkembangan</h2>
                </div>
            </div>
            <div class="priority-list">
                @forelse ($member->progressUpdates as $update)
                    <div class="priority-card">
                        <div class="priority-head">
                            <strong>{{ $update->area }}</strong>
                            <span>{{ $update->stage ?: 'Tanpa tahap' }} · {{ $update->status }}</span>
                        </div>
                        <p>{{ $update->summary }}</p>
                    </div>
                @empty
                    <p class="empty-state">Belum ada progress pembinaan.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="feature-card">
        <div class="section-heading">
            <div>
                <div class="mono-eyebrow">NOTE HISTORI</div>
                <h2>Catatan evaluasi</h2>
            </div>
        </div>
        <div class="priority-list">
            @forelse ($member->notes as $note)
                <div class="priority-card">
                    <div class="priority-head">
                        <strong>{{ $note->tag }}</strong>
                        <span>{{ $note->author->name }} · {{ $note->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <p>{{ $note->content }}</p>
                </div>
            @empty
                <p class="empty-state">Belum ada note untuk member ini.</p>
            @endforelse
        </div>
    </section>

    <section class="feature-card">
        <div class="section-heading">
            <div>
                <div class="mono-eyebrow">IURAN MEMBER</div>
                <h2>Riwayat finansial</h2>
            </div>
        </div>
        <div class="table-list">
            @forelse ($member->contributions as $contribution)
                <div class="table-row">
                    <div>
                        <strong>{{ $contribution->period_month }}/{{ $contribution->period_year }}</strong>
                        <p>Jatuh tempo {{ $contribution->due_date?->format('d M Y') ?: '-' }}</p>
                    </div>
                    <div>Tagihan Rp {{ number_format($contribution->amount_due, 0, ',', '.') }}</div>
                    <div>Bayar Rp {{ number_format($contribution->amount_paid, 0, ',', '.') }}</div>
                    <span class="status-pill">{{ $contribution->status }}</span>
                </div>
            @empty
                <p class="empty-state">Belum ada data iuran.</p>
            @endforelse
        </div>
    </section>
@endsection
