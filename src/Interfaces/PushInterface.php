<?php

namespace Victorlopezalonso\LaravelUtils\Interfaces;

/**
 * Interface PushInterface.
 */
interface PushInterface
{
    /**
     * Return an instance.
     *
     * @return $this
     */
    public static function make();

    /**
     * Set the content of the push notification.
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title);

    /**
     * Set the content of the push notification.
     *
     * @param $content
     *
     * @return $this
     */
    public function content($content);

    /**
     * Set the url to open when the user press the push notification.
     *
     * @param $url
     *
     * @return $this
     */
    public function url($url);

    /**
     * Set the userId of the push notification.
     *
     * @param array|string $ids
     *
     * @return $this
     */
    public function wherePushIdIn($ids);

    /**
     * Set a user tag that must exists with a given value.
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function whereTagEquals($key, $value);

    /**
     * Set a group of user tags that must exists.
     *
     * @param array|string $tags
     *
     * @return $this
     */
    public function whereTagIn($tags);

    /**
     * Set the event id of the push notification.
     *
     * @param int $id
     *
     * @return $this
     */
    public function eventId(int $id);

    /**
     * Set an additional param to send along with the push notification.
     *
     * @param string $name
     * @param $param
     *
     * @return $this
     */
    public function param(string $name, $param);

    /**
     * Set the data object of the push notification.
     *
     * @param array $data
     *
     * @return $this
     */
    public function data(array $data);

    /**
     * Return the params of the object.
     *
     * @return object
     */
    public function toJson();

    /**
     * Send a push notification.
     *
     * @return $this
     */
    public function send();
}
