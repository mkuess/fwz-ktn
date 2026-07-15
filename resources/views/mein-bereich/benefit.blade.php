@extends('layouts.minimal')

@section('title', $benefit->name . ' – Mein Bereich')

@section('content')

{{-- Dark header with membership card --}}
<div style="background:#1a2e1a;padding:2rem 1.5rem 1rem;display:flex;justify-content:center">
    @include('partials.mitgliedskarte', ['member' => $member])
</div>

{{-- Animated live indicator --}}
<div style="background:#1a2e1a;padding:0.5rem 1.5rem 1.25rem;display:flex;justify-content:center">
    <div style="display:flex;gap:4px;align-items:center">
        <span style="width:6px;height:6px;border-radius:50%;background:var(--yellow);animation:dot-pulse 1.4s ease-in-out infinite"></span>
        <span style="width:6px;height:6px;border-radius:50%;background:var(--yellow);animation:dot-pulse 1.4s ease-in-out 0.2s infinite"></span>
        <span style="width:6px;height:6px;border-radius:50%;background:var(--yellow);animation:dot-pulse 1.4s ease-in-out 0.4s infinite"></span>
        <span style="color:rgba(255,255,255,0.6);font-size:0.75rem;margin-left:0.5rem">Live-Ausweis</span>
    </div>
</div>

@push('styles')
<style>
@keyframes dot-pulse {
    0%, 100% { opacity: 0.3; transform: scale(0.8); }
    50%       { opacity: 1;   transform: scale(1.2); }
}
</style>
@endpush

{{-- Benefit detail card --}}
<div style="max-width:480px;margin:0 auto;padding:1.5rem">

    <div style="background:#fff;border-radius:1rem;padding:1.5rem;box-shadow:0 4px 16px rgba(0,0,0,0.1)">

        @if($benefit->logo_path)
            <img src="{{ Storage::url($benefit->logo_path) }}" alt="{{ $benefit->name }}"
                 style="max-height:60px;max-width:160px;object-fit:contain;margin-bottom:1rem;display:block">
        @endif

        <h1 style="font-size:1.25rem;font-weight:700;color:#1a2e1a;margin:0 0 0.75rem">{{ $benefit->name }}</h1>
        <p style="color:#374151;line-height:1.7;margin:0 0 1rem">{{ $benefit->description }}</p>

        @if($benefit->content)
            <div style="color:#374151;line-height:1.7">{!! $benefit->content !!}</div>
        @endif

        @if($benefit->website)
            <a href="{{ $benefit->website }}" target="_blank" rel="noopener"
               style="display:inline-block;margin-top:1.25rem;background:var(--yellow);color:#1a2e1a;padding:0.75rem 1.5rem;border-radius:2rem;text-decoration:none;font-weight:700">
                Zum Partner →
            </a>
        @endif

    </div>

    <a href="{{ route('member.portal') }}"
       style="display:block;text-align:center;margin-top:1.5rem;color:#6b7280;font-size:0.875rem;text-decoration:none">
        ← Zurück zu meinen Benefits
    </a>

</div>

@endsection
