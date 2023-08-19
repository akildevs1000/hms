<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotifications extends Model
{
    use HasFactory;

    public function report_type_access()
    {

        return $this->hasMany(NotificationReportAccess::class);
    }

    protected $fillable = [
        'email',
        'company_id',
        'status',
        'name',
        'whatsapp_number',
    ];

}
