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
    # [!] ACCESS_NOTIFICATION

    **Target_Entity:** {{ $user->email }}  
    **Event_Type:** SYSTEM_LOGIN  
    **Timestamp:** {{ now()->format('Y-m-d H:i:s') }}

    A successful login sequence was initiated for your account. If this was not authorized by your terminal, please secure your credentials immediately.

    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0; border: 1px solid #eeeeee;">
        <tr>
            <td style="padding: 20px; background-color: #000000; color: #ffffff; text-align: center;">
                <span style="font-size: 10px; font-weight: 900; letter-spacing: 4px; text-transform: uppercase;">Secure_Sync_Active</span>
            </td>
        </tr>
    </table>

    @component('mail::button', ['url' => config('app.url') . '/profile', 'color' => 'primary'])
        VIEW_SESSION_LOGS
    @endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            SECURE_SYNC_MMXXVI // NO_REPLY_ARCHIVE
        @endcomponent
    @endslot
@endcomponent