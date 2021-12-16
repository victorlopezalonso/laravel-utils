<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Victorlopezalonso\LaravelUtils\Jobs\PushJob;
use Victorlopezalonso\LaravelUtils\Interfaces\PushInterface;

class OneSignal implements PushInterface
{
    public const ONE_SIGNAL_URL = 'https://onesignal.com/api/v1/notifications';

    protected $params;

    /**
     * @param null $params
     */
    public function __construct($params = null)
    {
        $this->params = $params ?? [];
    }

    /**
     * Return an instance.
     *
     * @param null $params
     *
     * @return $this
     */
    public static function make($params = null)
    {
        return new self($params);
    }

    /**
     * Set the content of the push notification.
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->params['headings'] = $title;

        return $this;
    }

    /**
     * Set the content of the push notification.
     *
     * @param $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->params['contents'] = $content;

        return $this;
    }

    /**
     * Set the content of the push notification.
     *
     * @param $url
     *
     * @return $this
     */
    public function url($url)
    {
        if ($url) {
            $this->params['url'] = $url;
        }

        return $this;
    }

    /**
     * Set the userId of the push notification.
     *
     * @param array|string $ids
     *
     * @return $this
     */
    public function wherePushIdIn($ids)
    {
        $this->params['include_player_ids'] = \is_array($ids) ? $ids : [$ids];

        return $this;
    }

    /**
     * Set a user tag that must exists with a given value.
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function whereTagEquals($key, $value)
    {
        if (isset($this->params['filters']) && \count($this->params['filters'])) {
            $this->params['filters'][] = ['operator' => 'OR'];
        }

        $this->params['filters'][] = [
            'field' => 'tag',
            'key' => $key,
            'relation' => '=',
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Set a group of user tags that must exists.
     *
     * @param array|string $tags
     *
     * @return $this
     */
    public function whereTagIn($tags)
    {
        $tags = \is_array($tags) ? $tags : [$tags];

        foreach ($tags as $tag) {
            if (isset($this->params['filters']) && \count($this->params['filters'])) {
                $this->params['filters'][] = ['operator' => 'OR'];
            }

            $this->params['filters'][] = [
                'field' => 'tag',
                'key' => $tag,
                'relation' => 'exists',
            ];
        }

        return $this;
    }

    /**
     * Set a group of user tags that must exists.
     *
     * @param $key
     * @param $values
     *
     * @return $this
     */
    public function whereTagEqualsIn($key, $values)
    {
        $values = \is_array($values) ? $values : [$values];

        foreach ($values as $value) {
            if (isset($this->params['filters']) && \count($this->params['filters'])) {
                $this->params['filters'][] = ['operator' => 'OR'];
            }

            $this->params['filters'][] = [
                'field' => 'tag',
                'key' => $key,
                'relation' => '=',
                'value' => $value,
            ];
        }

        return $this;
    }

    /**
     * Set the event id of the push notification.
     *
     * @param int $id
     *
     * @return $this
     */
    public function eventId(int $id)
    {
        $this->params['data']['eventId'] = $id;

        return $this;
    }

    /**
     * Set the event id of the push notification.
     *
     * @param string $name
     * @param $param
     *
     * @return $this
     */
    public function param(string $name, $param)
    {
        $this->params['data'][$name] = $param;

        return $this;
    }

    /**
     * Set the data object of the push notification.
     *
     * @param array $data
     *
     * @return $this
     */
    public function data(array $data)
    {
        $this->params['data']['data'] = $data;

        return $this;
    }

    /**
     * Set custom property
     *
     * @param string $key property name
     * @param $value property value
     *
     * @return $this
     */
    public function setProperty(string $key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Return the params of the object.
     *
     * @return object
     */
    public function toJson()
    {
        $this->mount();

        return (object)get_object_vars($this);
    }

    /**
     * Send a push notification.
     */
    public function send()
    {
        $this->mount();

        if (app()->environment('testing')) {
            return;
        }

        $http = new Client();

        try {
            $http->request('POST', self::ONE_SIGNAL_URL, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . config('laravel-utils.onesignal.app_key'),
                    'Accept' => 'application/json',
                ],
                'json' => $this->params,
            ]);
        } catch (Exception $e) {
            Log::critical('ONESIGNAL ERROR: '.$e->getMessage());
        }
    }

    /**
     * Send a push notification using a job.
     */
    public function sendAsync()
    {
        $this->mount();

        PushJob::dispatch($this->params);
    }

    /**
     * Fill the params before send the push notification.
     */
    protected function mount()
    {
        $this->params['headings'] = $this->params['headings'] ?? ['en' => 'Title'];
        $this->params['contents'] = $this->params['contents'] ?? ['en' => 'Content'];

        $this->params['data']['eventId'] = $this->params['data']['eventId'] ?? 1;
        $this->params['data']['data'] = $this->params['data']['data'] ?? null;

        $this->params['app_id'] = config('laravel-utils.onesignal.app_id');

        if (!isset($this->params['include_player_ids']) && !isset($this->params['filters'])) {
            $this->params['included_segments'] = 'Subscribed Users';
        }
    }

    public function dump()
    {
        $this->mount();

        dd($this->params);
    }
}
