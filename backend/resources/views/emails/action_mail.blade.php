@component('mail::message')
    {!! $body !!}


    Thanks,
    {{ config('app.name') }}
@endcomponent
