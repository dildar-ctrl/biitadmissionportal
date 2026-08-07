<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'documenttype';

    public $timestamps = false;

    // NOTE: display-name column in `documenttype` is called `type`.
    protected $fillable = [
        'type',
    ];
}