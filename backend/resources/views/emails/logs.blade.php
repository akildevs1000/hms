@component('mail::message')
    # {{ $data["title"] }}

    {{ $data["body"] }}
    

    Thanks,
    {{ config('app.name') }}
@endcomponent
