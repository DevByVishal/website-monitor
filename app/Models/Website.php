<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Website extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'url',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}