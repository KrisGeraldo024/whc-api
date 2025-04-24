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
                <b>An inspired Suntrust Day!</b>
                <br>
                <br>
                <p>Thank you for reaching out to us. We’ve received your inquiry and are currently reviewing it. Our team will get back to you as soon as possible. We appreciate your patience and look forward to serving you.</p>
                <br>
                <br>
                <p>Thank you</p>
                <p>Suntrust Properties Inc.</p>
                <br>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>