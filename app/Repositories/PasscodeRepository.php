<?php

namespace App\Repositories;

use App\Models\UserPasscode;
use Hash;

class PasscodeRepository {
    public function StorePasscode($data,$user){
        $passcode =  UserPasscode::create([
            'user_id' => $user,
            'passcode' =>  Hash::make($data['passcode']),
        ]);
        return true;
    }
}
