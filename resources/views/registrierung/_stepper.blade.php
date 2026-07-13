<div class="reg-stepper">
  @php
    $steps = [
      1 => 'Organisation',
      2 => 'Benutzerkonto',
      3 => 'Standort & Kontakt',
    ];
  @endphp
  @foreach($steps as $num => $label)
    <div class="reg-step {{ $step == $num ? 'active' : ($step > $num ? 'done' : '') }}">
      <div class="reg-step__circle">
        @if($step > $num)
          <span aria-hidden="true">✓</span>
        @else
          {{ $num }}
        @endif
      </div>
      <span class="reg-step__label">{{ $label }}</span>
    </div>
    @if($num < 3)
      <div class="reg-step__line {{ $step > $num ? 'done' : '' }}"></div>
    @endif
  @endforeach
</div>
