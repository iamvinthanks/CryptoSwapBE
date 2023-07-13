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
    public function GetWallet($user){
        $wallet = UserWallet::where('user_id',$user)->select('trx_address','bsc_address')->first();
        return $wallet;
    }
    public function GetPrivate($user){
        $wallet = UserWallet::where('user_id',$user)->select('trx_private_key','bsc_private_key')->first();
        return $wallet;
    }
}
