<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    public $timestamps = false; // table uses `uploaded_at`, not created_at/updated_at

    protected $fillable = [
        'candidate_id',
        'education_id',
        'documenttype_id',
        'document_path',
        'uploaded_at',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'documenttype_id');
    }
}