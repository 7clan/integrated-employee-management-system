<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    protected $fillable =['name','location','description'];
    /** @use HasFactory<\Database\Factories\WorkplaceFactory> */
    use HasFactory;
    public function employees(){
        return $this->hasMany(Employee::class);
    }
}
