<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Welcome {{ $user->name }}</h2>

    <p>Thank you for registering.</p>

    <p>Your verification code is:</p>

    <h1>{{ $user->verification_code }}</h1>

    <p>Please enter this code to verify your account.</p>
</body>
</html>