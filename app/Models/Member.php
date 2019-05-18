<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'member';

    protected $primaryKey = 'code';
    
    public $incrementing = false;

    protected $fillable = array(
        'code',
        'date_of_registration'
    );

    public function person()
    {
        return $this->belongsTo('App\Models\Person');
    }

    public function sponsors()
    {
        return $this->belongsToMany('App\Models\Member', 'member_downline', 'member_code', 'sponsor_code')
        ->withTimestamps();
    }

    public function hasDownlines()
    {
        return (count($this->downlines)>0) ? true : false;
    }

    public function downlines()
    {
        return $this->belongsToMany('App\Models\Member', 'member_downline', 'sponsor_code', 'member_code')
        ->withTimestamps();
    }

    public function purchaseOrders()
    {
        return $this->hasMany('App\Models\PurchaseOrder', 'member_code');
    }
}
