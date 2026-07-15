<div style="width:100%;max-width:420px;aspect-ratio:1.586;border-radius:1rem;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.25)">

    <div style="position:absolute;inset:0;background-image:url('{{ asset('img/mitgliedskarte-bg.png') }}');background-size:cover;background-position:center"></div>

    <!-- Dark overlay for text readability -->
    <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(26,46,26,0.7) 0%,rgba(26,46,26,0.2) 70%,transparent 100%)"></div>

    <!-- Card content -->
    <div style="position:relative;z-index:1;padding:1.5rem;height:100%;display:flex;flex-direction:column;justify-content:space-between;box-sizing:border-box">

        <!-- Top: Logo -->
        <div>
            <img src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten" style="height:1.75rem;filter:brightness(0) invert(1)">
        </div>

        <!-- Middle: Member name + organisation -->
        <div>
            <div style="color:#ffffff;font-size:1.25rem;font-weight:700;line-height:1.2">
                {{ $member->first_name }} {{ $member->last_name }}
            </div>
            @if(isset($member->organisation) && $member->organisation)
                <div style="color:rgba(255,255,255,0.85);font-size:0.8rem;margin-top:0.25rem">
                    {{ is_object($member->organisation) ? $member->organisation->name : $member->organisation }}
                </div>
            @endif
        </div>

        <!-- Bottom: Membership number -->
        <div style="display:flex;justify-content:space-between;align-items:flex-end">
            <div style="color:rgba(255,255,255,0.9);font-size:0.85rem;font-family:monospace;letter-spacing:0.15em">
                {{ $member->formatted_membership_number ?? 'wird zugeteilt' }}
            </div>
        </div>

    </div>
</div>
