<button
  type="button"
  class="password-toggle"
  x-cloak
  @click="passwordVisible = !passwordVisible"
  :aria-label="passwordVisible ? 'Passwort ausblenden' : 'Passwort anzeigen'"
  :title="passwordVisible ? 'Passwort ausblenden' : 'Passwort anzeigen'"
  :aria-pressed="passwordVisible.toString()"
  aria-controls="{{ $fieldId }}"
>
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
    <circle cx="12" cy="12" r="3"></circle>
    <path x-show="passwordVisible" d="M3 3l18 18"></path>
  </svg>
</button>
