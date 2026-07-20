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
    # New Login Alert

    **Account:** {{ $user->email }}  
    **Activity:** Successful Login  
    **Timestamp:** {{ now()->format('Y-m-d H:i:s') }}

    A successful login was just recorded for your account. If you did this, you can safely ignore this email. If you did not authorize this login, please secure your account immediately.

    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0; border: 1px solid #eeeeee;">
        <tr>
            <td style="padding: 20px; background-color: #000000; color: #ffffff; text-align: center;">
                <span style="font-size: 10px; font-weight: 900; letter-spacing: 4px; text-transform: uppercase;">Account Security Active</span>
            </td>
        </tr>
    </table>

    @component('mail::button', ['url' => config('app.url') . '/profile', 'color' => 'primary'])
        View Account Settings
    @endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            World Star Store // Security Notification
        @endcomponent
    @endslot
@endcomponent