<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 11:55 AM
 */

namespace App\Transformers;


class NodeTransformer extends BaseTransformer
{
    public function transform($object)
    {
        return [
            'id' => $object->id,
            'name' => $object->name,

            'neighbours' => $object->neighbours()->count() > 0 ?
                app(EdgeTransformer::class)->transformCollection(
                    $object->neighbours()
                        ->paginate(50)
                        ->appends(['node_id' => $object->id])
                ) : (object)[]

        ];
    }
}