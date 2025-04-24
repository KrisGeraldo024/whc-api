<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        @include('mail.mail-css')
    </head>
    <body>
        <div class="wrapper">
            @include('mail.header')
            <div class="middle">
            <b>Hello {{ $data['first_name'] }} {{ $data['last_name'] }}!</b>
            <br>
            <br>
            <p>Thank you for your interest in joining the Suntrust Team! We are currently reviewing your application. Please keep your lines open, as we will reach out with updates soon. We’re excited about the possibility of working together and look forward to building a successful partnership.</p>
            <br>
            <br>
            <p>Sincerely,</p>
            <p>The Suntrust Team</p>
            <br>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>