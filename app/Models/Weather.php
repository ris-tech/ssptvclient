<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Weather extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $table = "weather";
    protected $fillable = [
        'location_id', 
        'vreme', 
        'vremetext', 
        'stepeni'
    ];
}