<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    // ✅ Yeh line add karein - timestamps disable karne ke liye
    public $timestamps = false;

    protected $table = 'education';

    protected $fillable = [
        'candidate_id',
        'degree',
        'selected_subject',  // ✅ Yeh bhi add karein (major subject ke liye)
        'date_of_passing',
        'markstype_id',
        'obtained_marks',
        'total_marks',
        'boarduniversity_id',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    // one education entry can have multiple major subjects
    public function majorSubjects()
    {
        return $this->belongsToMany(
            MajorSubject::class,
            'majorsubject',
            'education_id',
            'majorsubject_id'
        );
    }

    public function marksType()
    {
        return $this->belongsTo(MarksType::class, 'markstype_id');
    }

    public function boardUniversity()
    {
        return $this->belongsTo(BoardUniversity::class, 'boarduniversity_id');
    }
}