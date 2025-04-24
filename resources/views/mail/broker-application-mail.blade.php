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
                <p>A Broker Accreditation Application has been submitted.</p>
                <br>
                <br>
                <p>INQUIRY NUMBER: <strong>{{ $data['inquiry_number'] }}</strong></p>
                <br>
                <br>
                <p><b>First Name:</b> {{ $data['first_name'] }}</p>
                <p><b>Last Name:</b> {{ $data['last_name'] }}</p>
                <p><b>Contact Number:</b> {{ $data['contact_number'] }}</p>
                <br>
                <br>
                <p><b>Email Address:</b> {{ $data['email_address'] }}</p>
                <p><b>Broker/Realty Company:</b> {{ $data['company'] }}</p>
                <p><b>Priority Location:</b> {{ $data['location'] }}</p>
                <br>
                <br>
                <p><b>Subject:</b> {{ $data['subject'] }}</p>
                <p><b>Message:</b> {{ $data['message'] }}</p>
                <p><b>Date Submitted:</b> {{ $data['created_at'] }}</p>
                
                
                <br>
                <br>
                <p>Thank you</p>
                <p>System Email</p>
                <br>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>