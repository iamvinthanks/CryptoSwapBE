<?php

namespace App\Services;
use GuzzleHttp\Client;

Class CryptoRateServices {

    public function cryptoPrice($data){
        $client = new Client();
        $res = $client->request('GET', 'https://api.coingecko.com/api/v3/simple/price?ids='.$data['crypto_type'].'&vs_currencies=idr');
        $response = json_decode($res->getBody(),true);
        $crypto_price = $response[$data['crypto_type']]['idr'];
        $crypto_amount = $data['crypto_amount'];
        $crypto_total = $crypto_price * $crypto_amount;
        $rate_data = [
            'crypto_price' => $crypto_price,
            'crypto_total' => $crypto_total
        ];
        return $rate_data;
    }
}
