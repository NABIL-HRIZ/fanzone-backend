<?php
// Usage: php artisan tinker
// Paste this code in tinker to test the scan API

use Illuminate\Support\Facades\Http;

$token = '61|fy4awh9EjT6dotbJcgGfB4Ag2szfDP1FbDGTF26iea909ffa'; 


$response_valid = Http::withToken($token)->post('http://localhost:8000/api/scans', [
    'ticket_id' => 1, // Replace with a valid ticket_id
    'scan_status' => 'valid'
]);

print_r($response_valid->json());

// Test with an invalid ticket_id
$response_invalid = Http::withToken($token)->post('http://localhost:8000/api/scans', [
    'ticket_id' => 999999, // Use an invalid ticket_id
    'scan_status' => 'valid'
]);

print_r($response_invalid->json());

// Test with already scanned ticket_id (repeat the valid one)
$response_already = Http::withToken($token)->post('http://localhost:8000/api/scans', [
    'ticket_id' => 1, // Use the same valid ticket_id again
    'scan_status' => 'valid'
]);

print_r($response_already->json());
