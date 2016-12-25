<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 * Copyright (c) 2015-2016 Richard Henkenjohann
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync\Api;


use CleverreachSync\AbstractApi;
use CleverreachSync\Helper\Receiver;


class Groups extends AbstractApi
{

    /**
     * Current object instance (Singleton)
     *
     * @type AbstractApi
     */
    protected static $instance;


    /**
     * Get all groups
     *
     * @return \stdClass|null
     */
    public function getAll()
    {
        $request = $this->callEndpoint('/v1/groups.json');

        if ($this->hasRequestError($request)) {
            return null;
        }

        return json_decode($request->response);
    }


    /**
     * @param integer $group
     *
     * @return null|\stdClass
     */
    public function getAttributesForGroup($group)
    {
        return $this->getPropertyForGroup('attributes', $group);
    }


    /**
     * @param integer $group
     *
     * @return null|\stdClass
     */
    public function getBlacklistForGroup($group)
    {
        return $this->getPropertyForGroup('blacklist', $group);
    }


    /**
     * @param integer $intGroup
     *
     * @return null|\stdClass
     */
    public function getFiltersForGroup($intGroup)
    {
        return $this->getPropertyForGroup('filters', $intGroup);
    }


    /**
     * @param integer $group
     *
     * @return null|\stdClass
     */
    public function getReceiversForGroup($group)
    {
        return $this->getPropertyForGroup('receivers', $group);
    }


    /**
     * @param integer $group
     *
     * @return null|\stdClass
     */
    public function getAdvancedstatsForGroup($group)
    {
        return $this->getPropertyForGroup('advancedstats', $group);
    }


    /**
     * @param integer $group
     *
     * @return null|\stdClass
     */
    public function getFormsForGroup($group)
    {
        return $this->getPropertyForGroup('forms', $group);
    }


    /**
     * @param integer $group
     *
     * @return null|\stdClass
     */
    public function getStatsForGroup($group)
    {
        return $this->getPropertyForGroup('stats', $group);
    }


    /**
     * Create a new group and return its new id or false otherwise
     *
     * @param string $name The name of the group to create
     *
     * @return int|bool
     */
    public function createGroup($name)
    {
        $request = $this->callEndpoint('/v1/groups.json', array('name' => $name), 'POST');

        if ($this->hasRequestError($request)) {
            return false;
        }

        $json = json_decode($request->response);

        // Return the created group's id
        return (int)$json->id;
    }


    /**
     * @param integer  $groupId
     * @param Receiver $receiver
     *
     * @return bool
     */
    public function createNewReceiver($groupId, Receiver $receiver)
    {
        $request = $this->callEndpoint(
            sprintf('/v1/groups.json/%u/receivers/insert', $groupId),
            $receiver
        );

        if ($this->hasRequestError($request)) {
            return false;
        }

        return true;
    }


    /**
     * @param integer  $groupId The group's id
     * @param mixed    $poolId  The receiver's id or email
     * @param Receiver $receiver
     *
     * @return bool
     */
    public function updateReceiver($groupId, $poolId, Receiver $receiver)
    {
        $request = $this->callEndpoint(
            sprintf('/v1/groups.json/%u/receivers/%u', $groupId, $poolId),
            $receiver,
            'PUT'
        );

        if ($this->hasRequestError($request)) {
            return false;
        }

        return true;
    }


    /**
     * Delete receiver by a given id/email
     *
     * @param integer    $groupId The group's id
     * @param int|string $poolId  The receiver's id or email
     *
     * @return bool
     */
    public function deleteReceiver($groupId, $poolId)
    {
        $request = $this->callEndpoint(
            sprintf('/v1/groups.json/%u/receivers/%u', $groupId, $poolId),
            null,
            'DELETE'
        );

        if ($this->hasRequestError($request)) {
            return false;
        }

        return true;
    }


    /**
     * Delete group
     *
     * @param integer $groupId The group's id
     *
     * @return bool
     */
    public function deleteGroup($groupId)
    {
        $request = $this->callEndpoint(
            sprintf('/v1/groups.json/%u', $groupId),
            null,
            'DELETE'
        );

        if ($this->hasRequestError($request)) {
            return false;
        }

        return true;
    }


    /**
     * Delete all email addresses from group
     *
     * @param integer $groupId The group's id
     *
     * @return bool
     */
    public function truncateGroup($groupId)
    {
        $request = $this->callEndpoint(
            sprintf('/v1/groups.json/%u/receivers', $groupId),
            null,
            'DELETE'
        );

        if ($this->hasRequestError($request)) {
            return false;
        }

        return true;
    }


    /**
     * @param integer|string $receiver The receiver's pool id or email
     * @param integer        $group
     *
     * @return bool
     */
    public function setActiveForReceiverInGroup($receiver, $group)
    {
        return $this->setStateForReceiverInGroup('active', $receiver, $group);
    }


    /**
     * @param integer|string $receiver The receiver's pool id or email
     * @param integer        $group
     *
     * @return bool
     */
    public function setInactiveForReceiverInGroup($receiver, $group)
    {
        return $this->setStateForReceiverInGroup('inactive', $receiver, $group);
    }


    /**
     * @param string  $property The property to fetch from a specific group
     * @param integer $group    The group id
     *
     * @return \stdClass|null
     */
    protected function getPropertyForGroup($property, $group)
    {
        if (!in_array(
            $property,
            [
                'attributes',
                'blacklist',
                'filters',
                'receivers',
                'advancedstats',
                'forms',
                'stats',
            ]
        )
        ) {
            throw new \RuntimeException(sprintf('Property %s is not allowed to fetch via api', $property));
        }

        $request = $this->callEndpoint(sprintf('/v1/groups.json/%u/%s', $group, $property));

        if ($this->hasRequestError($request)) {
            return null;
        }

        return json_decode($request->response);
    }


    /**
     * @param string         $state
     * @param integer|string $receiver The receiver's pool id or email
     * @param integer        $group
     *
     * @return bool
     */
    protected function setStateForReceiverInGroup($state, $receiver, $group)
    {
        if (!in_array(
            $state,
            [
                'active',
                'inactive',
            ]
        )
        ) {
            throw new \LogicException(sprintf('State %s is not allowed to set via api', $state));
        }

        $request = $this->callEndpoint(
            sprintf('/v1/groups.json/%u/receivers/%s/set%s', $group, $receiver, $state),
            '',
            'PUT'
        );

        if ($this->hasRequestError($request)) {
            return false;
        }

        return ('true' === $request->response) ? true : false;
    }
}
