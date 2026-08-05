<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReport extends Model
{
    use HasFactory;

    protected $table = 'patient_report';
    protected $primaryKey = 'patient_report_id';
    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'file_name',
        'file_path',
        'file_size',
        'status',
    ];

    protected $appends = [
        'formatted_file_size',
        'formatted_upload_date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function getFilePathAttribute($value)
    {
        return $value ? asset('uploads/report/' . $value) : '';
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 1) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    public function getFormattedUploadDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : '';
    }
}
