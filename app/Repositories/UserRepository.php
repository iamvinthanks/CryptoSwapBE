<?php

namespace App\Repositories;
use App\Models\User;
use App\Models\UserPasscode;
use Illuminate\Support\Facades\Hash;

class UserRepository {
    public function CreateUser($data){
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
         ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $response = [
            'user' => $user->id,
            'access_token' => $token,
        ];
        return $response;
    }
}
