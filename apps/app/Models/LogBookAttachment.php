<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBookAttachment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['log_book_id', 'path'];

    public function logBook()
    {
        return $this->belongsTo(LogBook::class);
    }
}
