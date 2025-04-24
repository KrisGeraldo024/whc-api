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
                <p>Appointment No.: <b>{{ $data->appointment_no }}</b></p>
                <p>Appointment Type: <b>{{ $data->type == 'home-visit' ? 'Home Visit' : 'In Store Visit' }}</b></p>
                @if (!$data->is_other_service)
                    <p>Service: <b>{{ $data->service ? $data->service->title : 'N/A' }}</b></p>
                @else
                    <p>Others Services: <b>{{ $data->others_message }}</b></p>
                @endif
                @if ($data->branch)
                    <p>Branch: <b>{{ $data->branch->title }}</b></p>
                @endif
                <p>Name: <b>{{ $data->full_name }}</b></p>
                <p>Email Address: <b>{{ $data->email }}</b></p>
                <p>Contact No.: <b>{{ $data->contact_number }}</b></p>
                <p>Telephone No.: <b>{{ $data->telephone_number }}</b></p>
                @if ($data->type == 'home-visit')
                    <p>Address: <b>{{ $data->house_no }} {{ $data->street }} {{ $data->barangay }} {{ $data->city }}, {{ $data->region }} {{ $data->zip_code }}</b></p>
                @endif
                <br/>
                <br/>
                @if ($data->companion_details)
                    <b>Companion Details</b>
                    <br/>
                    <br/>
                    <p>Name: <b>{{ json_decode($data->companion_details)->first_name }} {{ json_decode($data->companion_details)->last_name }} </b></p>
                    <p>Contact Number: <b>{{ json_decode($data->companion_details)->contact_number }}</b></p>
                    <p>Email Address: <b>{{ json_decode($data->companion_details)->email }}</b></p>
                @endif
                <br/>
                <br/>
                <b>Background Questions</b>
                <br/>
                <br/>
                <table class="order_table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (json_decode($data->background_question) as $item)
                            <tr>
                                <td>{{ $item->question }}</td>
                                <td>{{ $item->answer }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br/>
                <br/>
                <b>Hearing Checkup Questions</b>
                <br/>
                <br/>
                <table class="order_table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (json_decode($data->checkup_question) as $item)
                            <tr>
                                <td>{{ $item->question }}</td>
                                <td>{{ $item->answer }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('mail.footer')
        </div>
    </body>
</html>