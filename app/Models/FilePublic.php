<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class FilePublic extends Model
{
    /**
     * Get the user associated with the FilePublic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
