<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class MailTemplate extends Model
{
    use HasSlug;
    protected $table = 'mail_templates';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'subject',
        'body',
        'attachments',
        'type',
        'status',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
