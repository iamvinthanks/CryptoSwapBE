<?php

namespace App\Repositories;

use App\Models\UserWallet;

class WalletRepository {
    public function StoreWallet($data, $user){

        $wallet = UserWallet::create([
            'user_id' => $user,
            'trx_address' => $data['trx_address'],
            'trx_private_key' => $data['trx_pk'],
            'bsc_address' => $data['bsc_address'],
            'bsc_private_key' => $data['bsc_pk'],
        ]);
        return true;
    }
}
