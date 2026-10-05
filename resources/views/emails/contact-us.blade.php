<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>New Contact Us Request</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f4f4f5; padding: 40px 15px;">

        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; overflow: hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #111827; padding: 30px; text-align: center;">

                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">
                                New Contact Us Request
                            </h1>

                            <p style="margin: 8px 0 0; color: #d1d5db; font-size: 14px;">
                                A new message has been submitted from your website
                            </p>

                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 35px 30px;">

                            <h2 style="margin: 0 0 20px; color: #111827; font-size: 20px;">
                                Contact Request Details
                            </h2>

                            {{-- Contact Information --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="border: 1px solid #e5e7eb; border-radius: 8px;">

                                {{-- Name --}}
                                <tr>
                                    <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; width: 35%; font-weight: bold; color: #374151;">
                                        Full Name
                                    </td>

                                    <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                        {{ $contact['full_name'] }}
                                    </td>
                                </tr>

                                {{-- Email --}}
                                <tr>
                                    <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #374151;">
                                        Email Address
                                    </td>

                                    <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb;">
                                        <a
                                            href="mailto:{{ $contact['email'] }}"
                                            style="color: #2563eb; text-decoration: none;"
                                        >
                                            {{ $contact['email'] }}
                                        </a>
                                    </td>
                                </tr>

                                {{-- User ID --}}
                                @if (!empty($contact['user_id']))
                                    <tr>
                                        <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #374151;">
                                            User ID
                                        </td>

                                        <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                            {{ $contact['user_id'] }}
                                        </td>
                                    </tr>
                                @endif

                                {{-- Department --}}
                                <tr>
                                    <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #374151;">
                                        Department
                                    </td>

                                    <td style="padding: 13px 15px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                        {{ $contact['department'] }}
                                    </td>
                                </tr>

                                {{-- Subject --}}
                                <tr>
                                    <td style="padding: 13px 15px; font-weight: bold; color: #374151;">
                                        Subject
                                    </td>

                                    <td style="padding: 13px 15px; color: #4b5563;">
                                        {{ $contact['subject'] }}
                                    </td>
                                </tr>

                            </table>

                            {{-- Message --}}
                            <h3 style="margin: 30px 0 12px; color: #111827; font-size: 17px;">
                                Message
                            </h3>

                            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; color: #4b5563; font-size: 14px; line-height: 1.7;">
                                {!! nl2br(e($contact['message'])) !!}
                            </div>

                            {{-- Reply Button --}}
                            <div style="margin-top: 30px; text-align: center;">

                                <a
                                    href="mailto:{{ $contact['email'] }}"
                                    style="display: inline-block; background-color: #111827; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-size: 14px; font-weight: bold;"
                                >
                                    Reply to {{ $contact['full_name'] }}
                                </a>

                            </div>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center;">

                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                This email was generated automatically from the
                                {{ config('app.name') }} Contact Us form.
                            </p>

                            <p style="margin: 6px 0 0; color: #9ca3af; font-size: 12px;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>
</html>