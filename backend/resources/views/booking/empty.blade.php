<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $heading ?? 'Email' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 6px;">
        {{-- <h2 style="color: #333;">{{ $heading ?? 'Hello' }}</h2> --}}
        <p style="font-size: 16px; color: #555;">{!! nl2br(e($body ?? 'No message provided.')) !!}</p>
        <br>
        {{-- <p style="font-size: 14px; color: #888;">Best regards,<br>Your Team</p> --}}
    </div>
</body>
</html>
