@once
<style>
@keyframes ripple {
    0% { transform: scale(0.8); opacity: 1; }
    100% { transform: scale(2.5); opacity: 0; }
}
@keyframes pulse-dot {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.3); opacity: 1; }
}
</style>
@endonce

<div style="width:100%;max-width:420px;aspect-ratio:900/583;border-radius:1rem;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.18)">

    <div style="position:absolute;inset:0;background-image:url('{{ asset('img/mitgliedskarte-bg.png') }}');background-size:100% 100%;background-position:center;background-repeat:no-repeat"></div>

    <!-- Card content -->
    <div style="position:relative;z-index:1;padding:1.5rem;height:100%;display:flex;flex-direction:column;justify-content:space-between;box-sizing:border-box">

        <!-- Top: Logo -->
        <div>
            <img src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten" style="height:1.75rem;width:auto">
        </div>

        <!-- Middle: Member name + organisation -->
        <div>
            <div style="color:#111111;font-size:1.25rem;font-weight:700;line-height:1.2">
                {{ $member->first_name }} {{ $member->last_name }}
            </div>
            @if(isset($member->organisation) && $member->organisation)
                <div style="color:#333333;font-size:0.8rem;margin-top:0.25rem">
                    {{ is_object($member->organisation) ? $member->organisation->name : $member->organisation }}
                </div>
            @endif
        </div>

        <!-- Bottom: Membership number -->
        <div style="display:flex;justify-content:space-between;align-items:flex-end">
            <div style="color:#111111;font-size:0.85rem;font-family:monospace;letter-spacing:0.15em">
                {{ $member->formatted_membership_number ?? 'wird zugeteilt' }}
            </div>
        </div>

    </div>
</div>
