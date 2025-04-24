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
                <p>An application has been submitted.</p>
                <br>
                <br>
                <p>INQUIRY NUMBER: <strong>{{ $data['inquiry_number'] }}</strong></p>
                <br>
                <br>
                <p><b>First Name:</b> {{ $data['first_name'] }}</p>
                <p><b>Last Name:</b> {{ $data['last_name'] }}</p>
                <p><b>Contact Number:</b> {{ $data['contact_number'] }}</p>
                <p><b>Email Address:</b> {{ $data['email_address'] }}</p>
                <p><b>Position Applying for:</b> {{ $data['job_title'] }}</p>
                <br>
                <p><b>Cover Letter:</b></p>
                <p>{{ $data['message'] }}</p>
                <br>
                <p>Please find attached resume of the application for your reference.</p>
                <br>
                <p>Sincerely,</p>
                <p>System Email</p>
                <br>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>