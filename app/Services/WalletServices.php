<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\PasscodeRepository;
use IEXBase\TronAPI\Tron;
use Illuminate\Support\Facades\Hash;

class WalletServices {
    public function __construct(UserRepository $userRepository,WalletRepository $walletRepository,PasscodeRepository $passcodeRepository){
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->passcodeRepository = $passcodeRepository;
    }
    function TronLibs(){
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        return $tron;
    }
    function bscScan(){
        $apikey = "EJ5V369RFQGYBG1K4UAV2ACVYCE9JRHDW5";
        $api = new \Binance\BscscanApi($apikey);
        $bnb = new \Binance\Bnb($api);
        return $bnb;
    }
    public function CryptoBalance($user){
        $wallet = $this->walletRepository->GetWallet($user);
        $data['tron_balance'] = $this->TronLibs()->getBalance($wallet['trx_address'],true);
        $data['bnb_balance'] = $this->bscScan()->bnbBalance($wallet['bsc_address']);
        return $data;
    }
}
