@extends($layout ?? 'layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <section class="mx-auto w-full max-w-4xl px-4 py-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-headline text-3xl font-extrabold text-slate-900">Notifikasi</h1>
                <p class="mt-1 text-sm text-slate-500">Informasi terbaru terkait proyek NusaTerang.</p>
            </div>

            @if(auth()->user()->unreadNotifications()->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-lg bg-primary-container px-4 py-2 text-sm font-bold text-on-primary-fixed hover:opacity-90">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="block border-b border-slate-100 last:border-b-0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="flex w-full items-start gap-4 px-5 py-4 text-left transition hover:bg-slate-50 {{ $isUnread ? 'bg-amber-50/60' : 'bg-white' }}">
                        <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $isUnread ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-headline text-sm font-bold text-slate-900">{{ $data['title'] ?? 'Notifikasi' }}</span>
                                @if($isUnread)
                                    <span class="rounded-full bg-red-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Belum dibaca</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Dibaca</span>
                                @endif
                            </span>
                            <span class="mt-1 block text-sm leading-6 text-slate-600">{{ $data['message'] ?? '-' }}</span>
                            <span class="mt-2 block text-xs font-medium text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                    </button>
                </form>
            @empty
                <div class="px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-slate-300">notifications_off</span>
                    <p class="mt-3 font-headline text-lg font-bold text-slate-700">Tidak ada notifikasi</p>
                    <p class="mt-1 text-sm text-slate-500">Notifikasi proyek akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </section>
@endsection
