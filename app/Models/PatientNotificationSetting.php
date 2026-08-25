<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientNotificationSetting extends Model
{
    protected $table = 'patient_notification_settings';
    protected $primaryKey = 'patient_notification_setting_id';

    protected $fillable = [
        'patient_id',
        'abnormal_marker_alert',
        'report_ready',
        'ai_health_insights',
        'lab_test_reminders',
        'weekly_health_digest',
        'health_tips_articles',
        'app_updates',
        'offers_promotions'
    ];

    protected $casts = [
        'abnormal_marker_alert' => 'boolean',
        'report_ready' => 'boolean',
        'ai_health_insights' => 'boolean',
        'lab_test_reminders' => 'boolean',
        'weekly_health_digest' => 'boolean',
        'health_tips_articles' => 'boolean',
        'app_updates' => 'boolean',
        'offers_promotions' => 'boolean'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
