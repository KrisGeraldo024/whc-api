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
                <b>Dear {{ $data['first_name'] }} {{ $data['last_name'] }}!</b>
                <br>
                <br>
                <p>Thank you for applying for the <b>{{ $data['job_title'] }}</b> role.</p>
                <br>
                <p>Our hiring team is currently reviewing all applications. If your qualifications align with our requirements, one of our recruiters will reach out to you. We encourage you to keep your contact information up-to-date and stay accessible during this time.</p>    
                <br>
                <p>We will keep you informed about the status of your application.</p>
                <br>
                <p>Wishing you all the best!</p>
                <br>
                <p>Sincerely,</p>
                <p>Recruitment Team</p>
                <br>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>