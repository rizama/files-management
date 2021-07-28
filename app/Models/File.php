<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * Get the status_approve associated with the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status_approve()
    {
        return $this->hasOne(StatusApprove::class, 'id', 'status_approve');
    }
}
