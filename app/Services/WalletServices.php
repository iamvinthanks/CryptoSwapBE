<?php

namespace App\Services;

use Web3\Web3;
use IEXBase\TronAPI\Tron;
use Web3\Providers\HttpProvider;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\WalletRepository;
use App\Repositories\PasscodeRepository;
use Web3\RequestManagers\HttpRequestManager;
use App\Repositories\UserTransactionRepository;


class WalletServices {
    public function __construct(UserRepository $userRepository,WalletRepository $walletRepository,PasscodeRepository $passcodeRepository,EncryptionServices $encryptionServices,UserTransactionRepository $userTransactionRepository){
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->passcodeRepository = $passcodeRepository;
        $this->encryptionServices = $encryptionServices;
        $this->userTransactionRepository = $userTransactionRepository;
    }

    function bscScan(){
        $apikey = "EJ5V369RFQGYBG1K4UAV2ACVYCE9JRHDW5";
        $api = new \Binance\BscscanApi($apikey);
        $bnb = new \Binance\Bnb($api);
        return $bnb;
    }
    function tronScan(){
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        return $tron;
    }
    public function CryptoBalance($user){
        $wallet = $this->walletRepository->GetWallet($user);

        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        $usdtContract =  $tron->toHex("TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs");
        $address = $tron->toHex($wallet['trx_address']);
        $abi = '[{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"value","type":"uint256"}],"name":"approve","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"sender","type":"address"},{"name":"recipient","type":"address"},{"name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"account","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"recipient","type":"address"},{"name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"owner","type":"address"},{"name":"spender","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"owner","type":"address"},{"indexed":true,"name":"spender","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Approval","type":"event"}]';
        $abiAry = json_decode($abi, true);
        $function = "decimals";
        $params = [];
        $result = $tron->getTransactionBuilder()->triggerConstantContract($abiAry,$usdtContract,$function, $params, $address);
        $decimals = $result[0]->toString();
        //get balance
        $function = "balanceOf";
        $params = [ str_pad($address,64,"0", STR_PAD_LEFT) ];
        $result = $tron->getTransactionBuilder()->triggerConstantContract($abiAry, $usdtContract,$function, $params, $address);
        $balance = $result[0]->toString();
        if (!is_numeric($balance)) {
            throw new Exception("Token balance not found");
        }

        $balance = bcdiv($balance, bcpow("10", $decimals), $decimals);
        $data['USDT_balance'] =(Float)$balance;
        $data['tron_balance'] = $tron->getBalance($wallet['trx_address'],true);
        $data['bnb_balance'] = (float)$this->bscScan()->bnbBalance($wallet['bsc_address']);
        $trx_transaction = $tron->getTransactionAddress($wallet['trx_address']);
        $data['usdt'] = $wallet['trx_address'];
        $data['bsc'] = $wallet['bsc_address'];
        $data['trx'] = [
            "address"=> $wallet['trx_address'],
            // "transaction"=> $trx_transaction,
        ];
        $data['bnb'] = [
            "address"=> $wallet['bsc_address'],
            // "transaction"=> $bnb_transaction,
        ];
        return $data;

    }
    public function SingleBalance($user, $cryptoType)
    {
        if($cryptoType == 'tron'){
            $wallet = $this->walletRepository->GetWallet($user);
            $balance = $this->tronScan()->getBalance($wallet['trx_address'],true);
            return $balance;
        }
    }
    public function sendFund($user,$type,$amount,$passcode,$cryptorate){
        $private = $this->walletRepository->GetPrivate($user);
        $wallet = $this->walletRepository->GetWallet($user);
        if($type == 'tron'){
            $privateKey = $private['trx_private_key'];
            $decrypt = $this->encryptionServices->decrypt($privateKey,$passcode);
            //interact with tron
            $trx = $this->tronScan();
            $trx->setPrivateKey($decrypt);
            $trx->setAddress($wallet['trx_address']);
            $note = base64_encode("Sent from");
            try{
                $result = $trx->sendTransaction('TKkVnB4AxtYukCqgKizS4pmNvLaSVf7GSv',floatval($amount),$trx->address['hex'],$note);
                $data = [
                    'transaction_type'=> 'sell',
                    'pair'=> 'idrtrx',
                    'transaction_amount'=> $amount,
                    'actual_rate'=> $cryptorate['crypto_price'],
                    'transaction_total'=> $amount * $cryptorate['crypto_price'],
                    'transaction_hash'=> $result['txID'],
                    'from_address'=> $wallet['trx_address'],
                    'transaction_status'=> 'pending',
                ];
                $saveData = $this->userTransactionRepository->StoreTransaction($data,auth()->user()->id);
            }catch(\IEXBase\TronAPI\Exception\TronException $e){
                dd($e->getMessage());
            }
            //end interaction
            return $saveData;
        }
        if($type == 'bnb'){

        }
    }
}
