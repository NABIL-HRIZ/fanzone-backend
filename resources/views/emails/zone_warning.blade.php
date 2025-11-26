<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zone Warning</title>
</head>
<body>
    <h2>{{ $type === 'full' ? 'Zone Complète !' : 'Zone Presque Pleine !' }}</h2>
    <p>
        Zone: <strong>{{ $zone->name }}</strong><br>
        Places restantes : <strong>{{ $zone->available_seats }}</strong>
    </p>
    @if($type === 'full')
        <p>Toutes les places sont maintenant réservées.</p>
    @else
        <p>Attention : il ne reste que quelques places !</p>
    @endif
</body>
</html>
