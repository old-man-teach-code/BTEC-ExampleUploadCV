<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'msv',
        'fullname',
        'cv_uploaded',
        'cv_filename',
    ];

    public function getFullnameAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function setMsvAttribute($value)
    {
        $this->attributes['msv'] = strtoupper(trim($value));
    }
}
