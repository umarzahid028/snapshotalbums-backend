<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_price',
        'plan_duration',
        'plan_no_of_ablums',
        'transaction_id',
        'transaction_status',
        'status',
        'trial_ends_at',
        'ends_at',
        'card_last_four',
        'card_exp_month',
        'card_exp_year',
        'payment_token',
    ];


    protected $dates = [
        'trial_ends_at',
        'ends_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
