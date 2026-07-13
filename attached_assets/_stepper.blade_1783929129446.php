@php($labels = ['Organisation', 'Benutzerkonto', 'Standort & Kontakt'])
<div class="stepper" role="list" aria-label="Fortschritt der Registrierung">
  @foreach($labels as $i => $label)
    @php($n = $i + 1)
    <div class="stepper-step {{ $n < $step ? 'is-done' : ($n === $step ? 'is-active' : '') }}" role="listitem">
      <span class="stepper-num" aria-hidden="true">{{ $n < $step ? '✓' : $n }}</span>
      <span>{{ $label }}</span>
    </div>
    @if(!$loop->last)
      <span class="stepper-line" aria-hidden="true"></span>
    @endif
  @endforeach
</div>
