<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket Reservation</title>
    <style>
        body {
           
            font-size: 14px;
            padding: 20px;
        }
        .container {
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 10px;
        }
        .qr {
            text-align: center;
            margin-top: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="title">Ticket de réservation</div>

    <p><strong>Match :</strong> {{ $reservation->fanZone->match->team_one_title }} vs {{ $reservation->fanZone->match->team_two_title }}</p>
    <p><strong>Status :</strong> {{ $reservation->payment_status }}</p>
    <p><strong>Zone :</strong> {{ $reservation->fanZone->name }}</p>
    <p><strong>Prix total :</strong> {{ $reservation->total_price }} DH</p>
    <p><strong>Date réservation :</strong> {{ $reservation->reservation_date }}</p>
    <p><strong>Nombre de tickets :</strong> {{ $reservation->number_of_tickets }}</p>
    <p><strong>Date du match :</strong> {{ $reservation->fanZone->match->match_date }}</p>
  

    <div class="qr">
        <img src="data:image/png;base64, {!! base64_encode($qrCode) !!}">
    </div>
</div>

</body>
</html>
