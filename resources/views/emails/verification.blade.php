<x-mail::message>
    <h2>Welcome to {{ config('app.name') }}!</h2>

    <p>Thank you for registering with us. To complete your registration and activate your account, we require you to
        verify your email address.</p>

    <p>Verification of your email ensures the security of your account and allows us to communicate important updates,
        notifications, and account-related information with you.</p>

    <p>Please click the button below to verify your email:</p>

    <a href="{{ $verificationUrl }}"
        style="background-color: #007bff; border: none; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block;">
        Verify My Email
    </a>

    <p>If you are unable to click the button, you can also copy and paste the following URL into your browser:</p>

    <p>{{ $verificationUrl }}</p>

    <p>If you did not create an account with us, please disregard this email.</p>

    <p>Thank you for choosing {{ config('app.name') }}!</p>

    <p>Sincerely,<br>
        The {{ config('app.name') }} Team</p>
</x-mail::message>
