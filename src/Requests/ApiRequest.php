<?php

namespace Victorlopezalonso\LaravelUtils\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ApiRequest.
 */
class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Returns the default transformers
     */
    public function transformers()
    {
        $validated = $this->validated();

        foreach ($validated as $key => &$value) {
            $value = camel_to_underscore($key);
        }

        return $validated;
    }

    /**
     * Add a new input value.
     *
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        $request = $this->all();

        $request[$key] = $value;

        $this->replace($request);

        $this->request->replace($request);
    }

    /**
     * Replace an input value.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $request = $this->all();

        $request[$key] = $value;

        $this->replace($request);
    }

    /**
     * Return the array with camelcase keys transformed to underscore.
     *
     * @param array $params
     *
     * @return array|string
     */
    public function params(...$params)
    {
        $request = $this->validated();

        if (!$params) {
            return camel_to_underscore($request, true);
        }

        $only = [];

        foreach ($params as $param) {
            if (isset($request[$param])) {
                $only[$param] = $request[$param];
            }
        }

        return camel_to_underscore($only, true);
    }

    /**
     * Return the array except some params with camelcase keys transformed to underscore
     *
     * @param array $params
     * @return array|string
     */
    public function ignoreParams(...$params)
    {
        return camel_to_underscore($this->except($params), true);
    }

    /**
     * @param $key
     *
     * @return array|mixed
     */
    public function swgArray($key)
    {
        $request = $this->get($key);

        return \is_string($request) ? explode(',', str_replace('"', '', $request)) : $request;
    }

    /**
     * Return the array with camelcase keys transformed to underscore.
     *
     * @param array $params
     *
     * @return array|string
     */
    public function transform(...$params)
    {
        $transformed = [];

        $transformers = $this->transformers();

        foreach ($this->validated() as $key => $value) {
            $transformed[$transformers[$key] ?? $key] = $value;
        }

        if (!$params) {
            return $transformed;
        }

        $only = [];

        foreach ($params as $param) {
            if (isset($transformed[$param])) {
                $only[$param] = $transformed[$param];
            }
        }

        return $only;
    }
}
