@php
    /** @var \Illuminate\Support\Carbon $target */
    /** @var string $size */
    $size = $size ?? 'md';
@endphp

<div class="countdown {{ $size === 'lg' ? 'countdown--lg' : '' }}"
     data-countdown="{{ $target->toIso8601String() }}"
     role="timer"
     aria-label="Time remaining until the event">
    <div class="countdown__unit">
        <span class="countdown__value" data-unit="days">--</span>
        <span class="countdown__label">Days</span>
    </div>
    <div class="countdown__unit">
        <span class="countdown__value" data-unit="hours">--</span>
        <span class="countdown__label">Hours</span>
    </div>
    <div class="countdown__unit">
        <span class="countdown__value" data-unit="minutes">--</span>
        <span class="countdown__label">Mins</span>
    </div>
    <div class="countdown__unit">
        <span class="countdown__value" data-unit="seconds">--</span>
        <span class="countdown__label">Secs</span>
    </div>
</div>
