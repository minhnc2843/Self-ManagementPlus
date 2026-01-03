<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDashboardSetting extends Model
{
    protected $fillable = ['user_id', 'banner_path', 'banner_title', 'banner_quote', 'banner_height',
    'banner_position_y', 'show_banner_title',
    'show_banner_quote',];
}