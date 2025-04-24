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
                <p>An inquiry has been submitted.</p>
                <br>
                <p><strong>INQUIRY NUMBER: {{ $data->inquiry_number }}</strong></p>
                <br>
                <p><b>First Name:</b> {{ $data->first_name }}</p>
                <p><b>Last Name:</b> {{ $data->last_name }}</p>
                <p><b>Contact Number:</b> {{ $data->contact_number }}</p>
                <br>
                @if($data->contact_type === 'lead')
                <p><b>Property Type:</b> {{ $data->property_type }}</p>
                <p><b>Community:</b> {{ $data->property_name }}</p>
                <p><b>Unit:</b> {{ $data->unit_type }}</p>
                @endif
                <br>
                <p><b>Email Address:</b> {{ $data->email_address }}</p>
                <p><b>Subject:</b> {{ $data->subject }}</p>
                <p><b>Message:</b> {{ $data->message }}</p>
                <p><b>Date Submitted:</b> {{ $data->created_at }}</p>
                
                
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