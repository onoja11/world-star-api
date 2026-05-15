@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <span style="color: #000000; font-weight: 900; letter-spacing: -1px; font-style: italic;">
                WORLD<span style="border: 1px solid #000000; padding: 0 5px; margin-left: 4px;">STAR</span>
            </span>
        @endcomponent
    @endslot

    {{-- Body --}}
    # [!] ACQUISITION_SUCCESS

    **Entity_Designation:** {{ $user->name }}  
    **Archive_ID:** {{ $user->id }}  
    **Status:** ACTIVE_MEMBER

    Welcome to the syndicate. Your identity has been successfully synced with our central archive. You now have full access to re-engineered essentials.

    @component('mail::button', ['url' => config('app.url') . '/caps', 'color' => 'success'])
        ACCESS_ARCHIVE
    @endcomponent

    <div style="background-color: #f9f9f9; padding: 20px; border-left: 4px solid #000000; margin: 20px 0;">
        <p style="font-size: 10px; font-weight: 900; color: #aaaaaa; letter-spacing: 2px; margin: 0;">LOG_NOTE</p>
        <p style="font-size: 12px; font-style: italic; margin: 5px 0 0 0;">"Re-engineered essentials for the culture. Luxury meets the streets."</p>
    </div>

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} WORLD STAR SYNDICATE. [6.5244° N, 3.3792° E]
        @endcomponent
    @endslot
@endcomponent