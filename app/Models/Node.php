<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = [
        'name'
    ];

    public function connectedTo()
    {
        return $this->hasMany(Edge::class, 'from_node_id');
    }

    public function connectedFrom()
    {
        return $this->hasMany(Edge::class, 'to_node_id');
    }

    public function neighbours()
    {
        return $this->hasMany(Edge::class, 'from_node_id');
    }
}
