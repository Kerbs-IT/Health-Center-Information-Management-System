<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pregnancy_history_questions extends Model
{
    protected $fillable = [
        'prenatal_case_record_id',
        'number_of_children',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
        'q2_answer1',
        'q2_answer2',
        'q2_answer3',
        'q2_answer4',
        'q2_answer5',

    ];

    public function prenatal_case_record(){
        return $this-> belongsTo(prenatal_case_records::class,'prenatal_case_record_id','id');
    }
}
