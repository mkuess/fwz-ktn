<div style="width:100%;max-width:420px;aspect-ratio:1.586;border-radius:1rem;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.25);font-family:inherit">

    @if(file_exists(public_path('img/mitgliedskarte-bg.png')))
        <img src="{{ asset('img/mitgliedskarte-bg.png') }}"
             alt=""
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
    @else
        <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1a2e1a 0%,#2d4a2d 50%,#c9a227 100%)"></div>
    @endif

    <!-- Dark overlay for text readability -->
    <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(26,46,26,0.75) 0%,rgba(26,46,26,0.3) 60%,transparent 100%)"></div>

    <!-- Card content -->
    <div style="position:relative;z-index:1;padding:1.5rem;height:100%;display:flex;flex-direction:column;justify-content:space-between;box-sizing:border-box">

        <!-- Top: Logo -->
        <div>
            <img src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten" style="height:1.75rem">
        </div>

        <!-- Middle: Member name + organisation -->
        <div>
            <div style="color:rgba(255,255,255,0.8);font-size:0.7rem;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:0.25rem">Freiwilliges Mitglied</div>
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
                {{ $member->membership_number ?? 'wird zugeteilt' }}
            </div>
            <div style="color:rgba(255,255,255,0.7);font-size:0.7rem;text-align:right">
                <div>FREIWILLIGENZENTRUM</div>
                <div>KÄRNTEN</div>
            </div>
        </div>

    </div>
</div>
