<?php

namespace App\Services;
use App\Repositories\UserTransactionRepository;
Class CryptoswapServices{
    public function __construct(UserTransactionRepository $userTransactionRepository){
        $this->userTransactionRepository = $userTransactionRepository;
    }
    public function CheckIncomingTron($data){
        $checktxid = $this->userTransactionRepository->validateTXID($data);
        dd($checktxid);
    }
}
