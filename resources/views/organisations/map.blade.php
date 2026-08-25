@extends('layouts.app')

@section('title', 'Karte der Vereine & Organisationen – Freiwilligenzentrum Kärnten')
@section('meta_description', 'Entdecke Vereine und Organisationen in Kärnten auf der interaktiven Karte des Freiwilligenzentrums.')

@push('styles')
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
  >
@endpush

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">VEREINE &amp; ORGANISATIONEN</span>
    <h1 class="h2">Engagement in Kärnten entdecken.</h1>
    <p>Finde Vereine und Organisationen in deiner Nähe und informiere dich direkt über ihre Angebote und Kontaktdaten.</p>
  </div>
</div>
@endsection

@section('content')
  <section class="section">
    <div class="container">
      <div class="box organisation-map-box">
        <div class="organisation-map-header">
          <div>
            <span class="eyebrow">Interaktive Karte</span>
            <h2 class="h2">Vereine &amp; Organisationen in Kärnten</h2>
            <p>Bewege den Mauszeiger über einen Pin oder tippe darauf, um die Kontaktdaten zu sehen.</p>
          </div>
          <a class="btn light-on-light" href="{{ route('organisations.index') }}">Listenansicht <span class="arrow">→</span></a>
        </div>

        @if($organisations->isEmpty())
          <div class="map-empty" role="status">
            <span class="map-empty-icon" aria-hidden="true">⌖</span>
            <h3>Noch keine Kartenstandorte verfügbar</h3>
            <p>Derzeit sind keine freigeschalteten Vereine mit gespeicherten Koordinaten vorhanden.</p>
            <a class="btn dark" href="{{ route('organisations.index') }}">Vereine &amp; Organisationen ansehen <span class="arrow">→</span></a>
          </div>
        @else
          <div
            id="organisations-map"
            class="organisation-map"
            role="application"
            aria-label="Interaktive Karte der Vereine und Organisationen in Kärnten"
          ></div>
          <p class="map-attribution-note">Kartendaten © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap-Mitwirkende</a></p>
        @endif
      </div>
    </div>
  </section>
@endsection

@if($organisations->isNotEmpty())
  @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      (function () {
        'use strict';

        var organisations = @json($organisations);
        var fallbackLogo = @json(asset('img/placeholder-verein-logo.svg'));
        var map = L.map('organisations-map', {
          minZoom: 7,
          maxZoom: 18,
          scrollWheelZoom: false,
        }).setView([46.75, 13.9], 8);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap-Mitwirkende</a>',
        }).addTo(map);

        function escapeHtml(value) {
          var element = document.createElement('div');
          element.textContent = value == null ? '' : String(value);
          return element.innerHTML;
        }

        function contactLine(icon, label, value, href) {
          if (!value) return '';

          return '<div class="map-popup-line">' +
            '<span aria-hidden="true">' + icon + '</span>' +
            '<a href="' + href + '">' + escapeHtml(value) + '</a>' +
          '</div>';
        }

        function popupContent(organisation) {
          var address = [organisation.street, [organisation.zip, organisation.city].filter(Boolean).join(' ')].filter(Boolean);
          var logo = organisation.logo_url || fallbackLogo;
          var website = organisation.website_url
            ? '<div class="map-popup-line"><span aria-hidden="true">🌐</span><a href="' +
              escapeHtml(organisation.website_url) +
              '" target="_blank" rel="noopener">' +
              escapeHtml(organisation.website) +
              '</a></div>'
            : '';

          return '<div class="map-popup">' +
            '<img class="map-popup-logo" src="' + escapeHtml(logo) + '" alt="" onerror="this.src=\'' + escapeHtml(fallbackLogo) + '\'">' +
            '<div class="map-popup-content">' +
              '<strong class="map-popup-name">' + escapeHtml(organisation.name) + '</strong>' +
              (address.length ? '<div class="map-popup-address">📍 ' + address.map(escapeHtml).join('<br>') + '</div>' : '') +
              contactLine('✉️', 'E-Mail', organisation.email, 'mailto:' + encodeURIComponent(organisation.email || '')) +
              contactLine('📞', 'Telefon', organisation.phone, 'tel:' + encodeURIComponent(organisation.phone || '')) +
              website +
            '</div>' +
          '</div>';
        }

        organisations.forEach(function (organisation) {
          var marker = L.marker([organisation.latitude, organisation.longitude], {
            title: organisation.name,
            alt: organisation.name,
          }).addTo(map);
          var content = popupContent(organisation);

          marker.bindTooltip(content, {
            direction: 'top',
            sticky: true,
            opacity: 0.98,
            className: 'organisation-tooltip',
          });
          marker.bindPopup(content, {
            maxWidth: 340,
            className: 'organisation-popup',
          });
          marker.on('focus', function () {
            marker.openTooltip();
          });
          marker.on('blur', function () {
            marker.closeTooltip();
          });
        });
      }());
    </script>
  @endpush
@endif