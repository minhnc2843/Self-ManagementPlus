<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDashboardSetting extends Model
{
    protected $fillable = ['user_id', 'banner_path', 'banner_title', 'banner_quote'];
}