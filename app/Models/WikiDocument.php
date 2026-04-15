<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WikiDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'tags',
        'estado',
        'user_id',
        'categoria'
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
