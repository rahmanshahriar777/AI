<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToArray;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BusinessInfo extends Model
{
    use HasSlug;

    protected $table = 'business_info';
    protected $fillable = [
        'business_name',
        'business_short_name',
        'business_domain',
        'business_allowed_file_types',
        'business_logo',
        'business_logo_dark',
        'business_favicon',
        'business_address',
        'business_address2',
        'business_city',
        'business_state',
        'business_zip',
        'business_country',
        'business_phone',
        'business_email',
        'business_website',
        'business_registration_number',
        'business_registration_number2',
        'business_status',
        'business_start_date',
        'business_brand_app_name',
        'business_brand_logo_on_top_left',
        'business_brand_credit_on_footer_enable',
        'business_brand_credit_on_footer',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('business_name')
            ->saveSlugsTo('business_slug');
    }

    public function getAllowedFileType($type='IMG', $remove_dot=false){
        $business_allowed_file_types = $this->business_allowed_file_types;
        if(empty($business_allowed_file_types)) return '';
        
        $result = [];

        $parts = preg_split('/\s+/', $business_allowed_file_types);

        foreach ($parts as $part) {
            [$key, $values] = explode(':', $part, 2);
            $result[$key] = array_filter(explode(',', $values));
        }
        $types = $result[$type];
        
        $typestring = '';
        if($types){
            if($remove_dot){
                $types = array_map(function($item) {
                    return trim($item, "."); // Trims '!' and '*'
                }, $types);
            }
            $typestring = implode(',', $types);
        }
        return $typestring;
    }
}
