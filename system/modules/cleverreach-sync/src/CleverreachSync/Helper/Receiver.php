<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 * Copyright (c) 2015-2016 Richard Henkenjohann
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync\Helper;


/**
 * @property string  $email
 * @property integer $activated
 * @property integer $registered
 * @property integer $deactivated
 * @property string  $source
 */
class Receiver implements \JsonSerializable
{

    /**
     * @var array
     */
    protected $data;


    /**
     * @var array
     */
    protected $baseAttributes = array
    (
        'email',
        'activated',
        'registered',
        'deactivated',
        'source',
    );


    /**
     * Set source base attribute
     */
    public function __construct()
    {
        $this->source = 'Contao Open Source CMS';
    }


    /**
     * Set a attribute
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case 'email':
                if (!\Validator::isEmail($value)) {
                    throw new \RuntimeException('No valid email submitted');
                }

                break;

            case 'activated':
            case 'registered':
            case 'deactivated':
                if (!\Validator::isNumeric($value)) {
                    throw new \RuntimeException(sprintf('No valid timestamp for "%s" submitted', $key));
                }

                break;
        }

        $this->data[$key] = $value;
    }


    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        $return = [];

        foreach ($this->data as $key => $value) {
            if (in_array($key, $this->baseAttributes)) {
                $return[$key] = $value;
                continue;
            }

            $return['global_attributes'][$key] = $value;
        }

        if (!strlen($return['email'])) {
            throw new \RuntimeException('No email for recipient submitted');
        }

        return $return;
    }
}
