<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 18/6/18
 * Time: 12:55 PM
 */

namespace App\Transformers;


class PathTransformer extends BaseTransformer
{
    public function transform($array)
    {
        $paths = [];

        foreach ($array as $key => $node) {
            $paths[] = [
                'id' => $node['id'],
                'name' => $node['name'],
            ];
        }

        return $paths;
    }
}