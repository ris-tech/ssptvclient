<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tv extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id'
    ];

    public function location()
    {
      return $this->belongsTo(Location::Class,'location_id');
    }
}
