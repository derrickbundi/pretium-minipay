<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinipayPaybill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    const PENDING = 'PENDING';
    const FAILED = 'FAILED';
    const COMPLETE = 'COMPLETE';
    const REFUNDED = 'REFUNDED';
    public function minipay_user() {
        return $this->belongsTo(MinipayUser::class);
    }
}
