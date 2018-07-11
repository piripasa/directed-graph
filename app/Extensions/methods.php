<?php

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

if (!function_exists('responsableConverter')) {
    function responsableConverter($response)
    {
        if ($response instanceof Responsable) {
            return json_decode($response->toResponse(app(Request::class))->getContent(), true);
        } else {
            return $response;
        }
    }
}

if (!function_exists('searchArray')) {
    function searchArray($array, $field, $value)
    {
        foreach($array as $key => $element)
        {
            if ( $element[$field] === $value )
                return $element;
        }
        return false;
    }
}
