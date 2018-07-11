<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 12:55 PM
 */

namespace App\Transformers;


class EdgeTransformer extends BaseTransformer
{
    public function transform($object)
    {
        return [
            'id' => $object->inDegree->id,
            'name' => $object->inDegree->name
        ];
    }
}