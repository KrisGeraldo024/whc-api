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
                @if ($data->subscribe)
                    <p>You are now subscribed to Active Hearing Center.</p>
                @else
                    <p>You are now unsubscribed to Active Hearing Center.</p>
                @endif
                <br>
                @if ($data->subscribe)
                    <p>Click <a href="{{ config('app.website_url') }}/unsubscribe?t={{ $data->id }}">here</a> to unsubscribe.</p>
                @endif
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