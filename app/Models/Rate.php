<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    const LUMEN_TYPE = 'x';
    const EXCHANGE_TYPE = 'e';
    const ETH_TYPE = 't';
    const CELO_TYPE = 'c';
    
    public function country() {
        return $this->belongsTo(Country::class);
    }
}
