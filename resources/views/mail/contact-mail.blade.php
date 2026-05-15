<!DOCTYPE html>
<html>
<head>
    <style>
        /* Note: Most email clients ignore <style> tags, so I've used inline styles below as well */
        .external-link { color: #ffffff; text-decoration: none; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: 'Helvetica', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <!-- Main Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border: 1px solid #000000;">
                    
                    <!-- Header: Black Bar -->
                    <tr>
                        <td bgcolor="#0a0a0a" style="padding: 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <h1 style="color: #ffffff; font-size: 24px; font-weight: 900; letter-spacing: -1px; margin: 0; text-transform: uppercase; font-style: italic;">
                                            WORLD<span style="border: 1px solid #ffffff; padding: 0 5px; margin-left: 4px;">STAR</span>
                                        </h1>
                                    </td>
                                    <td align="right">
                                        <span style="color: #444444; font-size: 8px; font-weight: 900; tracking: 2px; text-transform: uppercase;">Inbound_Comm_v1.0</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Alert Strip -->
                    <tr>
                        <td style="padding: 20px 40px; border-bottom: 1px solid #eeeeee; background-color: #fafafa;">
                            <span style="font-size: 10px; font-weight: 900; color: #000000; letter-spacing: 3px; text-transform: uppercase; font-style: italic;">
                                [!] New_Archive_Entry_Detected
                            </span>
                        </td>
                    </tr>

                    <!-- Content Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Identity Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td width="50%" style="padding-bottom: 20px;">
                                        <p style="margin: 0; font-size: 9px; font-weight: 900; color: #aaaaaa; text-transform: uppercase; letter-spacing: 2px; font-style: italic;">Entity_Designation</p>
                                        <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: 900; color: #000000; text-transform: uppercase;">{{ $data['firstname'] }} {{ $data['lastname'] }}</p>
                                    </td>
                                    <td width="50%" style="padding-bottom: 20px;">
                                        <p style="margin: 0; font-size: 9px; font-weight: 900; color: #aaaaaa; text-transform: uppercase; letter-spacing: 2px; font-style: italic;">Comm_Channel</p>
                                        <p style="margin: 5px 0 0 0; font-size: 14px; font-weight: 700; color: #000000;">{{ $data['email'] }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message Block -->
                            <div style="background-color: #000000; padding: 30px; border-left: 4px solid #444444;">
                                <p style="margin: 0 0 10px 0; font-size: 9px; font-weight: 900; color: #666666; text-transform: uppercase; letter-spacing: 2px; font-style: italic;">Narrative_Payload</p>
                                <p style="margin: 0; font-size: 15px; line-height: 24px; color: #ffffff; font-weight: 400;">
                                    "{{ $data['message'] }}"
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer Details -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #eeeeee; padding-top: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; font-size: 8px; font-weight: 900; color: #cccccc; text-transform: uppercase; letter-spacing: 2px;">Timestamp_Ref: {{ date('Y-m-d H:i:s') }}</p>
                                    </td>
                                    <td align="right">
                                        <a href="{{ url('/') }}" style="font-size: 9px; font-weight: 900; color: #000000; text-decoration: none; text-transform: uppercase; border: 1px solid #000000; padding: 5px 10px;">Return_To_Base</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Technical Watermark -->
                    <tr>
                        <td bgcolor="#f4f4f4" align="center" style="padding: 20px;">
                            <p style="margin: 0; font-size: 7px; color: #bbbbbb; text-transform: uppercase; letter-spacing: 5px; font-weight: 900;">
                                MMXXVI_SYNDICATE_SECURE_SYNC
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>