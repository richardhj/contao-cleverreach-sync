<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 * Copyright (c) 2015-2016 Richard Henkenjohann
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync\Helper;


use CleverreachSync\Api\Groups;
use CleverreachSync\Api\Receivers;


class Hooks
{

    /**
     * Add receiver to Cleverreach
     *
     * @param string $email      The recipient's email
     * @param array  $recipients An array containing the recipient ids (multiple ids if multiple channels selected)
     * @param array  $channels   An array containing the channel ids (multiple ids if multiple channels selected)
     */
    public function activateRecipient($email, $recipients, $channels)
    {
        $receiver = new Receiver();
        $receiver->email = $email;
        $receiver->activated = time();

        foreach ($channels as $cid) {
            /** @type \Model $channel */
            $channel = \NewsletterChannelModel::findByPk($cid);

            if ($channel->cr_group_id) {
                if (false === Groups::getInstance()->createNewReceiver($channel->cr_group_id, $receiver)) {
                    \System::log(
                        sprintf('Could not activate/insert recipient %s to CR group %u', $email, $channel->cr_group_id),
                        __METHOD__,
                        TL_ERROR
                    );
                }
            }
        }
    }


    /**
     * Delete receiver from Cleverreach
     *
     * @param string $email  The recipient's e mail address
     * @param array  $remove An array containing the channel ids to remove
     */
    public function removeRecipient($email, $remove)
    {
        foreach ($remove as $cid) {
            /** @type \Model $channel */
            $channel = \NewsletterChannelModel::findByPk($cid);

            if ($channel->cr_group_id) {
                // Delete from group
                if (false === Receivers::getInstance()->deleteOneByIdOrEmail($email, $channel->cr_group_id)) {
                    \System::log(
                        sprintf('Could not delete recipient %s from CR group %u', $email, $channel->cr_group_id),
                        __METHOD__,
                        TL_ERROR
                    );
                }

                //@todo add to blacklist to prohibit re-add
            }
        }

    }


    /**
     * Sync local member with group associated cleverreach groups
     * @category save_callback (field: groups)
     *
     * @param mixed          $value The submitted groups as serialized string
     * @param \DataContainer $dc
     *
     * @return mixed
     */
    public function syncMemberGroupsWithCleverreach($value, $dc)
    {
        $groups = deserialize($value);

        $groupsNew = $groups ?
            \Database::getInstance()
                ->query('SELECT cr_group_id FROM tl_member_group WHERE id IN('.implode(',', $groups).') AND cr_sync=1')
                ->fetchEach('cr_group_id')
            : array();

        $groupsOld = \Database::getInstance()
            ->prepare(
                'SELECT g.cr_group_id FROM tl_member_to_group AS mtg INNER JOIN tl_member_group g ON g.id=mtg.group_id WHERE mtg.member_id=? AND g.cr_sync=1'
            )
            ->execute($dc->id)
            ->fetchEach('cr_group_id');

        /** @type \Model $member */
        $member = \MemberModel::findByPk($dc->id);

        # $objMember        contains obsolete data (pre save)
        # $dc->activeRecord contains current data

        // Create receiver helper object
        $receiver = new Receiver();

        try {
            foreach ($member->row() as $k => $v) {
                $receiver->$k = $v;
            }
        } catch (\RuntimeException $e) {
            \System::log(
                sprintf('Could not create recipient instance for member ID %u', $member->id),
                __METHOD__,
                TL_ERROR
            );

            return $value;
        }

        // Update receiver in these groups
        foreach (array_intersect($groupsNew, $groupsOld) as $group) {
            Groups::getInstance()->updateReceiver($group, $member->cr_receiver_id, $receiver);
        }

        // Create receiver in these groups
        foreach (array_diff($groupsNew, $groupsOld) as $group) {
            if (Groups::getInstance()->createNewReceiver($group, $receiver)) {
                $member->cr_receiver_id = Receivers::getInstance()->getOneByIdOrEmail(
                    $dc->activeRecord->email,
                    $group
                )->id;
                $member->save();
            }
        }

        // Delete receiver in these groups
        foreach (array_diff($groupsOld, $groupsNew) as $group) {
            Groups::getInstance()->deleteReceiver($group, $member->cr_receiver_id);
        }

        return $value;
    }
    

    public function syncNewsletterRecipientsWithReceivers($dc)
    {

        // Only synchronize in list view
//		if ($dc->id)
//		{
//			return;
//		}

//		/** @type \Model $objNewsletterChannel */
//		$objNewsletterChannel = \NewsletterChannelModel::findByPk($dc->id);
//
//		$a = Groups::getInstance()->getReceiversForGroup($objNewsletterChannel->cr_group_id);
//		dump($a);
//
//		foreach ($a as $objReceiver)
//		{
//			$objNew = new \NewsletterRecipientsModel();
//			$objNew->
//		}

    }
}
