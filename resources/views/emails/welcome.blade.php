<x-mail::message>
# Bienvenue, {{ $user->first_name }} 

Merci de rejoindre **FanZone** !

Nous sommes ravis de t’avoir parmi nous.

<x-mail::button :url="''">
Découvrir FanZone
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
