<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Code</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f6f4; font-family:Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f3f6f4; padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                   style="max-width:520px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e2e8e4;">

                <tr>
                    <td align="center" style="background-color:#0c7347; padding:26px 20px;">
                        @if(file_exists(public_path('logo.png')))
                            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="BIIT"
                                 width="64" height="64" style="display:block; border-radius:50%; margin:0 auto 10px;">
                        @endif
                        <div style="color:#ffffff; font-size:18px; font-weight:700;">BIIT Admission Portal</div>
                        <div style="color:#cfe8d8; font-size:12px; margin-top:4px;">Barani Institute of Information Technology</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px 28px 8px; color:#16231c; font-size:15px; line-height:1.6;">
                        <p style="margin:0 0 12px;">Hello {{ $name }},</p>
                        <p style="margin:0 0 18px;">
                            We received a request to reset your password. Use the code below to continue.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:0 28px;">
                        <div style="background-color:#e6f4ec; border:1px dashed #0c7347; border-radius:10px; padding:18px 10px;">
                            <span style="font-size:34px; font-weight:700; letter-spacing:10px; color:#0c7347;">{{ $otp }}</span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 28px 28px; color:#5b6b63; font-size:13px; line-height:1.6;">
                        <p style="margin:0 0 8px;">This code expires in <strong>{{ $expiryMinutes }} minutes</strong>.</p>
                        <p style="margin:0 0 8px;">Never share this code with anyone, including BIIT staff.</p>
                        <p style="margin:0;">If you did not request a password reset, you can safely ignore this email.</p>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="background-color:#f9fafb; padding:14px; color:#8a978f; font-size:11px; border-top:1px solid #e2e8e4;">
                        &copy; {{ date('Y') }} Barani Institute of Information Technology
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>