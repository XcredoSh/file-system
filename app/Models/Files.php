<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'files';

    protected $fillable = [
        'user_id',
        'path',
        'path_url',
        'type',
        'size',
        'file_name',
        'hash_summa',
        'parent_id',
        'hash_name',
        'ext'
    ];

    /**
     * Get the post that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
