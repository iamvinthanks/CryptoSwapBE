<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }
}
