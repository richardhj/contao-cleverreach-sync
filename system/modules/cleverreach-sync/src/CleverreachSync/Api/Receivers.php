<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 * Copyright (c) 2015-2016 Richard Henkenjohann
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync\Api;


use CleverreachSync\AbstractApi;


class Receivers extends AbstractApi
{

    /**
     * Current object instance (Singleton)
     *
     * @type AbstractApi
     */
    protected static $instance;


    /**
     * @param integer|string $receiver The receiver's pool id or email
     * @param integer        $group    The group id the search should be limited to
     *
     * @return \stdClass|null
     */
    public function getOneByIdOrEmail($receiver, $group)
    {
        $endpoint = sprintf(
            '/v1/receivers.json/%s%s',
            $receiver,
            ($group) ? '?group_id='.$group : ''
        );

        $request = $this->callEndpoint($endpoint);

        if ($this->hasRequestError($request)) {
            return null;
        }

        return json_decode($request->response);
    }


    /**
     * @param integer|string $receiver The receiver's pool id or email
     * @param integer        $group    The group id the action should be restricted to
     *
     * @return bool
     */
    public function deleteOneByIdOrEmail($receiver, $group)
    {
        $endpoint = sprintf(
            '/v1/receivers.json/%s%s',
            $receiver,
            ($group) ? '?group_id='.$group : ''
        );

        $request = $this->callEndpoint($endpoint, [], 'DELETE');

        if (!$this->hasRequestError($request)) {
            return ('true' === $request->response) ? true : false;
        }

        return false;
    }
}
