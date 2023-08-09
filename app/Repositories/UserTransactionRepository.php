<?php

namespace App\Repositories;

use App\Models\UserTransaction;
use App\Models\UserWallet;

Class UserTransactionRepository {
    public function StoreTransaction($data,$user){

        $transaction =  UserTransaction::create([
            'user_id' => $user,
            'transaction_type' => $data['transaction_type'],
            'pair' => $data['pair'],
            'transaction_amount' => $data['transaction_amount'],
            'actual_rate' => $data['actual_rate'],
            'transaction_total' => $data['transaction_total'],
            'transaction_hash' => $data['transaction_hash'],
            'from_address' => $data['from_address'],
            'transaction_status' => $data['transaction_status'],
        ]);
        return true;
    }
    public function validateTXID($data){
        $gettxid = UserTransaction::where('transaction_hash',$data['txId'])->get();
        // dd($gettxid);
        $checkduplicate = $gettxid->count();
        if($checkduplicate < 1){
            return false;
        }
        if($checkduplicate > 1){
            return false;
        }
        if($checkduplicate == 1){
            if($gettxid[0]->transaction_status == 'on progress'){
                $checkwallet = UserWallet::where('user_id',$gettxid[0]['user_id'])->first();
                if($checkwallet['trx_address'] == $gettxid[0]['from_address']){
                    $gettxid[0]->transaction_status = 'on progress';
                    $gettxid[0]->save();

                    return true;
                }
                return false;
            }
            return false;
        }
    }
}
