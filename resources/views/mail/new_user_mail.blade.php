<x-mail::message>
# Subject: {{ $data['subject'] }}
# Full Name: {{ $data['first_name'] . ' ' . $data['last_name']}}
# Username: {{ $data['email'] }}
# Message: {{ $data['message'] }}
<x-mail::button :url="'https://suntrust-cms.designbluemanila.com/'">
    Click here to login
</x-mail::button>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>