<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 *
 * Copyright (c) 2015-2016 Richard Henkenjohann
 *
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync\Api;


use CleverreachSync\AbstractApi;


class Blacklist extends AbstractApi
{

    /**
     * Current object instance (Singleton)
     *
     * @type AbstractApi
     */
    protected static $instance;


    public function getAll()
    {
        return [];
    }


    public function getOneByEmail($email)
    {
        return [];
    }


    public function addEmail($email)
    {
        return true;
    }


    public function deleteEmail($email)
    {
        return true;
    }
}
