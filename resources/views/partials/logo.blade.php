@if ($logoUrl)
    <span class="logo-chip"><img src="{{ $logoUrl }}" alt="{{ $logoText }}"></span>
@else
    <span class="logo-word">{{ $logoText }}</span>
@endif
