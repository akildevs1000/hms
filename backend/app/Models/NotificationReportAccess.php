<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationReportAccess extends Model
{
    use HasFactory;
    protected $table = 'notification_report_access';

    public function report_type()
    {
        return $this->belongsTo(NotificationReportTypes::class, 'notification_report_types_id', 'id');

    }

    protected $fillable = [

        'company_id',
        'email_notifications_id',
        'notification_report_types_id',
    ];

}
