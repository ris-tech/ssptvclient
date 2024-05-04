<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;
    protected $table = "slides";
    protected $fillable = [
        'location_id',
        'tv_id',
        'slide_title',
        'slide_content',
        'sorting'
    ];

    public function location()
    {
      return $this->belongsTo(Location::Class,'location_id');
    }
    
    public function tv()
    {
      return $this->belongsTo(Tv::Class,'tv_id');
    }

    public function slideImage()
    {
      return $this->hasMany(SlideImage::Class,'slide_id')->latest();
    }

    public function slideImages()
    {
      return $this->hasMany(SlideImage::Class,'slide_id');
    }
    
}
