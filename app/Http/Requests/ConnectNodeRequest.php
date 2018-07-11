<?php

namespace App\Http\Requests;

class ConnectNodeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_node' => 'required|exists:nodes,id',
            'to_node' => 'required|exists:nodes,id',
        ];
    }
}
