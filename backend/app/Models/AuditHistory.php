<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ["data" => "array"];

    protected $appends = ["file_absoulte_path"];

    public function getFileAbsoultePathAttribute()
    {
        // Ensure the file_name exists before proceeding
        if (empty($this->file_name)) {
            return null;
        }

        // Generate the path
        $date = request("date", date("Y-m-d")); // Default to today's date if not provided
        $companyId = request("company_id", 0);  // Default to company_id 0 if not provided

        // Build the file path dynamically
        return storage_path("app/pdf/{$date}/{$companyId}/{$this->file_name}.pdf");
    }
}
