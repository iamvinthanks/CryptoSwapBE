<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletServices;
class WalletController extends Controller
{
    public function __construct(WalletServices $walletServices){
        $this->walletServices = $walletServices;
    }
    public function CryptoBalance(){
        $user = auth()->user()->id;
        $res = $this->walletServices->CryptoBalance($user);

        return response()->json([
            'success' => true,
            'message' => 'Crypto Balance',
            'data' => $res
        ], 200);
    }
}
