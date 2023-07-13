<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CryptoswapServices;
use App\Services\WalletServices;
use App\Services\CryptoRateServices;
use Validator;
use Auth;
use Illuminate\Support\Facades\Log;

class CryposwapController extends Controller
{
    public function __construct(CryptoswapServices $cryptoswapServices,CryptoRateServices $cryptoRateServices,WalletServices $walletServices){
        $this->cryptoswapServices = $cryptoswapServices;
        $this->walletServices = $walletServices;
        $this->cryptoRateServices = $cryptoRateServices;

    }
    public function confirmationBeforeswap(Request $request){
        $data = $request->all();
        $validate = Validator::make($data,[
            "crypto_type" => "required",
            "crypto_amount" => "required",
            "bank_account" => "required",
        ]);
        if($validate->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validate->errors()
            ], 400);
        }
        $checkbalance = $this->walletServices->SingleBalance(auth()->user()->id,$data['crypto_type']);
        if($checkbalance < $data['crypto_amount'] || $checkbalance == 0 || $checkbalance < 10){
            return response()->json([
                'success' => false,
                'message' => 'Insufficient Balance',
                'data' => $checkbalance
            ], 400);
        }
        $cryptorate = $this->cryptoRateServices->cryptoPrice($data);
        return response()->json([
            'success' => true,
            'message' => 'Crypto Balance',
            'data' => $cryptorate
        ], 200);
    }
    public function cryptoToidr(Request $request){
        $data = $request->all();
        if($data['crypto_type'] == 'tron'){
            $checkbalance = $this->walletServices->SingleBalance(auth()->user()->id,$data['crypto_type']);
            if($checkbalance < $data['crypto_amount'] || $checkbalance == 0 || $data['crypto_amount'] < 10){
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient Balance',
                    'data' => $checkbalance
                ], 400);
            }
            $cryptorate = $this->cryptoRateServices->cryptoPrice($data);
            $sendfund = $this->walletServices->sendFund(auth()->user()->id,$data['crypto_type'],$data['crypto_amount'],$data['passcode'],$cryptorate);
            return response()->json([
                'success' => true,
                'message' => 'Crypto Balance',
                'data' => $sendfund
            ], 200);
        }
    }
    public function tronCallback(Request $request){
        $data = $request->all();
        $verifpayment = $this->cryptoswapServices->CheckIncomingTron($data);
    }
}
