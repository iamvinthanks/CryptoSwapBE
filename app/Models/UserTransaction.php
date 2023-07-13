<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_transaction';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];


    protected $fillable = [
        'user_id',
        'transaction_type',
        'pair',
        'transaction_amount',
        'actual_rate',
        'transaction_total',
        'transaction_hash',
        'from_address',
        'transaction_status',
    ];
}
