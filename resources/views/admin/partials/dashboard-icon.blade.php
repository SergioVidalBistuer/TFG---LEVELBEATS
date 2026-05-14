@switch($name)
    @case('users')
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M16 18.5c0-2.2-1.8-4-4-4H8c-2.2 0-4 1.8-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="10" cy="7.5" r="3.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M18 17.5c1.4-.4 2.4-1.7 2.4-3.2 0-1.8-1.4-3.2-3.2-3.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('music')
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 18V6l10-2v12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="6" cy="18" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="16" cy="16" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break
    @case('receipt')
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-3 2-3-2-3 2-3-2V5a2 2 0 0 1 2-2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M8 8h8M8 12h8M8 16h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('folder')
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3.5 7.5a2 2 0 0 1 2-2h4l2 2h7a2 2 0 0 1 2 2v7.5a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break
    @case('collection')
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M5 7.5h14M5 12h14M5 16.5h14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M4 4h16v16H4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break
    @default
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M19.4 13.5a7.8 7.8 0 0 0 0-3l2-1.5-2-3.4-2.4 1a8.2 8.2 0 0 0-2.6-1.5L14 2.5h-4l-.4 2.6A8.2 8.2 0 0 0 7 6.6l-2.4-1-2 3.4 2 1.5a7.8 7.8 0 0 0 0 3l-2 1.5 2 3.4 2.4-1a8.2 8.2 0 0 0 2.6 1.5l.4 2.6h4l.4-2.6a8.2 8.2 0 0 0 2.6-1.5l2.4 1 2-3.4Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
@endswitch
