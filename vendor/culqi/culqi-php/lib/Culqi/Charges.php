<?php

namespace Culqi;

/**
 * Class Charges
 *
 * @package Culqi
 */
class Charges extends Resource
{

    const URL_CHARGES = "/charges/";

    /**
     * @param array|null $options
     *
     * @return all Charges.
     */
    public function all($options = null)
    {
        return $this->request("GET", self::URL_CHARGES, $api_key = $this->culqi->api_key, $options);
    }

    /**
     * @param array|null $options
     *
     * @return create Charge response.
     */
    public function create($options = null)
    {
        return $this->request("POST", self::URL_CHARGES, $api_key = $this->culqi->api_key, $options);
    }

    /**
     * @param string|null $id
     *
     * @return get a Charge.
     */
    public function get($id = null)
    {
        return $this->request("GET", self::URL_CHARGES . $id . "/", $api_key = $this->culqi->api_key);
    }

    /**
     * @param string|null $id
     *
     * @return get a capture of Charge.
     */
    public function capture($id = null)
    {
        return $this->request("POST", self::URL_CHARGES . $id . "/capture/", $api_key = $this->culqi->api_key);
    }

    /**
     * @param string|null $id
     * @param array|null  $options
     *
     * @return update Charge response.
     */
    public function update($id = null, $options = null)
    {
        return $this->request("PATCH", self::URL_CHARGES . $id . "/", $api_key = $this->culqi->api_key, $options);
    }

}
