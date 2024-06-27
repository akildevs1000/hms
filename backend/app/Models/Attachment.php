<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // attachment


    public function getAttachmentAttribute($val)
    {
        return asset("attachments/" . $this->model . "/" . $this->expense_id . "/" . $val);
    }
}
