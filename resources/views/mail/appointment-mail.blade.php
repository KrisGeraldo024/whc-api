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
                <b>Hello!</b>
                <br>
                <br>
                <p>Your appointment ({{ $data->appointment_no }}) has been submitted.</p>
                <br>
                <p>We have received your appointment. Kindly allow us 1-2 days to check your appointment.</p>
                <br>
                <br>
                <p>Thank you</p>
                <p>Active Hearing Center</p>
                <br>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>