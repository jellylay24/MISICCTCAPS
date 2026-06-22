<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['name', 'description', 'quantity', 'is_available'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    public function availableQuantity()
    {
        $borrowed = $this->borrows()
            ->whereIn('status', ['approved', 'borrowed'])
            ->sum('quantity');
        return $this->quantity - $borrowed;
    }
}
