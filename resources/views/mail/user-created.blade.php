@component('mail::message')
# Account Created Email for {{ $user->name }}

with email adrress

{{ $user->email }}


Welcome on board!

@component('mail::button', ['url' => '#'])
View my Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
