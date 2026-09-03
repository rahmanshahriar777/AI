<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    protected $fillable = [
        'customer_id',
        'company_name',
        'registration_number',
        'assets',
        'net_assets',
        'liabilities',
        'cash_in_bank',
        'asset_details_url',
    ];
}
