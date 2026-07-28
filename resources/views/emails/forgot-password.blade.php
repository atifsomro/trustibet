<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <p>Hello {{ $user->name }},</p>

    <p>Click the button below to reset your password.</p>

    <a href="{{ $link }}">
        Reset Password
    </a>

    <p>This link will expire in 1 hour.</p>
</body>

</html>