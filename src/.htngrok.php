<?php

$json = json_decode(file_get_contents("http://ngrok:4040/api/tunnels"));
$public_url = $json->tunnels[0]->public_url;
$channel_access_token = getenv("CHANNEL_ACCESS_TOKEN");

$curl = curl_init("https://api.line.me/v2/bot/channel/webhook/endpoint");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
    "endpoint" => "$public_url",
]));
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $channel_access_token",
]);
curl_exec($curl);
curl_close($curl);

echo "$public_url\n";
