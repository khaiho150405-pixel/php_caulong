
<div class="empty-state {{ ($compact ?? true) ? 'is-compact' : 'bg-court' }}">
    <svg class="empty-icon" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="32" cy="52" r="7" fill="currentColor" fill-opacity=".12"/>
        <path d="M26 46 14 12M32 45V8M38 46l12-34"/>
        <path d="M14 12Q32 4 50 12"/>
        <path d="M21 30q11-4 22 0"/>
    </svg>
    <p class="empty-text">{{ $message }}</p>
    @isset($linkUrl)
        <a href="{{ $linkUrl }}" class="btn btn-primary btn-sm">{{ $linkText ?? 'Quay lại' }}</a>
    @endisset
</div>
