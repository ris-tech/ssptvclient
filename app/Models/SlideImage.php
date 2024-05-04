<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlideImage extends Model
{
    use HasFactory;
    protected $table = 'slide_images';
    protected $fillable = [
      'location_id',
      'tv_id',
      'slide_id',
      'tv_img',
      'sorting'];

    public function location()
    {
      return $this->belongsTo(Location::Class,'location_id');
    }
    
    public function tv()
    {
      return $this->belongsTo(Tv::Class,'tv_id');
    } 
    
    public function slide()
    {
      return $this->belongsTo(Slide::Class,'slide_id');
    } 

}
