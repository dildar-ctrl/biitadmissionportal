<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $table = 'testquestion';
    public $timestamps = false;

    protected $fillable = ['test_id', 'question_id', 'category_id'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}