<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 *
 * Copyright (c) 2015-2016 Richard Henkenjohann
 *
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync\Helper;


use CleverreachSync\Api\Groups;


class Dca
{

    /**
     * Get all cleverreach groups
     * @category options_callback
     *
     * @return array
     */
    public function getCleverreachGroups()
    {
        $return = array();
        $groups = Groups::getInstance()->getAll();

        if (null !== $groups) {
            foreach ($groups as $group) {
                $return[$group->id] = $group->name;
            }
        }

        return $return;
    }


    /**
     * Remove a member from all groups after deleting
     * @category ondelete_callback
     *
     * @param $dc
     */
    public function deleteMember(\DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        $groups = \Database::getInstance()
            ->prepare(
                'SELECT g.cr_group_id FROM tl_member_to_group AS mtg INNER JOIN tl_member_group g ON g.id=mtg.group_id WHERE mtg.member_id=? AND g.cr_sync=1'
            )
            ->execute($dc->id)
            ->fetchEach('cr_group_id');

        foreach ($groups as $group) {
            Groups::getInstance()->deleteReceiver($group, $dc->activeRecord->cr_receiver_id);
        }
    }


    /**
     * Remove a group's members from the cleverreach group after deleting a member group
     * @category ondelete_callback
     *
     * @param $dc
     */
    public function deleteMemberGroup(\DataContainer $dc)
    {
        if (!$dc->id || !$dc->activeRecord->cr_sync) {
            return;
        }

        $members = \Database::getInstance()
            ->prepare(
                'SELECT m.cr_group_id FROM tl_member AS m INNER JOIN tl_member_to_group mg ON m.id=mg.member_id WHERE mg.group_id=?'
            )
            ->execute($dc->id)
            ->fetchEach('cr_group_id');

        foreach ($members as $member) {
            Groups::getInstance()->deleteReceiver($dc->activeRecord->cr_group_id, $member);
        }
    }


    /**
     * Sync (create and delete) local newsletter channels with cleverreach groups
     * @category onload_callback
     *
     * @param \DataContainer $dc
     */
    public function syncNewsletterChannelsWithGroups(\DataContainer $dc)
    {
        // Only synchronize in list view
        if ($dc->id) {
            return;
        }

        $groups = Groups::getInstance()->getAll();

        if (null === $groups) {
            return;
        }

        // Create groups
        foreach ($groups as $group) {
            if (null === ($channelExisting = \NewsletterChannelModel::findBy('cr_group_id', $group->id))) {
                /** @type \Model $channelNew */
                $channelNew = new \NewsletterChannelModel();
                $channelNew->title = $group->name;
                $channelNew->tstamp = $group->stamp;
                $channelNew->cr_group_id = $group->id;
                $channelNew->save();
            } else {
                $channelExisting->title = $group->name;
                $channelExisting->tstamp = $group->stamp;
                $channelExisting->save();
            }
        }
//
//
//        $channels = \NewsletterChannelModel::findBy(['cr_group_id<>0'], []);
//        $toDelete = array_diff($groups)
        // Delete groups
    }
}
