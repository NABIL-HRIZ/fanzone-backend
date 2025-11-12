<x-mail::message>
# Salut cher fan de football ! 

Ceci est un rappel pour le match **{{ $match->team_one_title }} vs {{ $match->team_two_title }}**  
qui aura lieu le {{ $match->match_date }} au {{ $match->stadium }}.

Ne manquez pas ce match passionnant !

Merci,<br>
{{ config('app.name') }}
</x-mail::message>