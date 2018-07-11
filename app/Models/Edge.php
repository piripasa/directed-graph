<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edge extends Model
{
    protected $fillable = [
        'from_node_id',
        'to_node_id'
    ];

    public function inDegree()
    {
        return $this->belongsTo(Node::class, 'to_node_id');
    }

    public function outDegree()
    {
        return $this->belongsTo(Node::class, 'from_node_id');
    }
}
