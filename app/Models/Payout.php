<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $table = 'payout';

    protected $fillable = array(
        'payout_from',
        'payout_to'
    );
}
