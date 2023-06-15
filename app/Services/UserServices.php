<?php

namespace App\Services;
use IEXBase\TronAPI\Tron;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\PasscodeRepository;
use App\Services\EncryptionServices;
use DB;
use Hash;

class UserServices{
    public function __construct(UserRepository $userRepository,WalletRepository $walletRepository,PasscodeRepository $passcodeRepository,EncryptionServices $encryptionServices)
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->passcodeRepository = $passcodeRepository;
        $this->encryptionServices = $encryptionServices;
    }
    public function createUser($request){
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        DB::beginTransaction();
        try {
            $user_data = $request;
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
            $wallet = new \Binance\Wallet();
            $trx_data = $tron->createAccount()->getRawData();
            $bsc_data = $wallet->newAccountByPrivateKey();
            $data['trx_address'] = $trx_data['address_base58'];
            $data['bsc_address'] = $bsc_data['address'];
            $data['trx_pk'] =  $this->encryptionServices->Encryption($user_data['passcode'],$trx_data['private_key']);
            $data['bsc_pk'] =  $this->encryptionServices->Encryption($user_data['passcode'],$bsc_data['key']);
            $res = $this->userRepository->createUser($user_data);
            $this->walletRepository->StoreWallet($data,$res['user']);
            $this->passcodeRepository->StorePasscode($user_data,$res['user']);
            DB::commit();
        } catch (\Exception  $e) {
            DB::rollback();
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return response()->json([
                    'status' => 'error',
                    'message' => 'User already exists',
                    'data' => []
                ],201);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $res
        ],200);
    }
}
