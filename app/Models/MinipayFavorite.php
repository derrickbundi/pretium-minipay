<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinipayFavorite extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    const PAYBILL = 'PAYBILL';
    const BUY_GOODS = 'BUY_GOODS';
    const SEND_MONEY = 'SEND_MONEY';
}
