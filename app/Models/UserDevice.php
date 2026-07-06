<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = 'user_device';
    protected $primaryKey = 'user_device_id';

    protected $fillable = [
        'user_id',
        'user_type',
        'app_version',
        'os_version',
        'device_name',
        'push_notification_id',
        'device_type',
    ];
}
