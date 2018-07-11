<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

/**
 * Class BaseRequest
 *
 * @package App\Http\Requests
 */
abstract class BaseRequest extends Request
{
    use ProvidesConvenienceMethods;

    /**
     * @var \Laravel\Lumen\Application|mixed
     */
    public $request;
    protected $includeRouteParameters = false;

    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->request = app('Illuminate\Http\Request');
        $message = $this->messages();
        if ($this->includeRouteParameters) {
            $route_parameters = $this->request->route()[2];
            $this->request->merge($route_parameters);
        }
        $this->validateRequestData($this->request, $message);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = [
        ];

        switch ($this->request->method()) {
            case 'GET':
                $rules = $this->getRules();
                break;
            case 'POST':
                $rules = $this->postRules();
                break;
            case 'PATCH':
                $rules = $this->patchRules();
                break;
            case 'DELETE':
                $rules = $this->deleteRules();
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
     * @return array
     */
    protected function getRules()
    {
        return [
        ];
    }

    /**
     * @return array
     */
    protected function postRules()
    {
        return [
        ];
    }

    /**
     * @return array
     */
    protected function patchRules()
    {
        return [
        ];
    }

    /**
     * @return array
     */
    protected function deleteRules()
    {
        return [
        ];
    }

    /**
     * @param $request
     * @param array|null $message
     */
    public function validateRequestData($request, array $message = null)
    {
        $rules = $this->rules();
        if (null !== $message) {
            $this->validate($request, $rules, $message);
        } else {
            $this->validate($request, $rules);
        }
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Unset inputs from the request by key
     *
     * @param array $inputs
     */
    public function unsetInputs(array $inputs)
    {
        $data = $this->request->all();
        foreach ($inputs as $input) {
            unset($data[$input]);
        }
        $this->request->replace($data);
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function input($key = null, $default = null)
    {

        return $this->request->input($key, $default);
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->request->has($key);
    }
}
