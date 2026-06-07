<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSetting extends Model
{
    protected $fillable = [
        'blog_name',
        'bio',
        'avatar',
        'bg_color',
        'text_color',
        'accent_color',
        'font',
    ];
}
