<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'skill', 'bio', 'workplace_id'];
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;
    public function workplace(){
        return  $this->belongsTo(Workplace::class);
    }
}
