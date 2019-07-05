<?php

namespace TickTackk\ChangeContentOwner\XF\Entity;

use TickTackk\ChangeContentOwner\Entity\ContentInterface;
use XF\Entity\User as UserEntity;

/**
 * Class Thread
 *
 * @package TickTackk\ChangeContentOwner\XF\Entity
 *
 * RELATIONS
 * @property Forum Forum
 */
class Thread extends XFCP_Thread implements ContentInterface
{
    /**
     * @param UserEntity|null $newUser
     * @param null            $error
     *
     * @return bool
     */
    public function canChangeOwner(UserEntity $newUser = null, &$error = null): bool
    {
        $forum = $this->Forum;
        if (!$forum)
        {
            return false;
        }

        return $forum->canChangePostOwner($newUser, $error);
    }

    /**
     * @param null $error
     *
     * @return bool
     */
    public function canChangeDate(&$error = null): bool
    {
        $forum = $this->Forum;
        if (!$forum)
        {
            return false;
        }

        return $forum->canChangeThreadDate($error);
    }

    /**
     * @param UserEntity|null $newOwner
     * @param null            $error
     *
     * @return bool
     */
    public function canChangePostOwner(UserEntity $newOwner = null, &$error = null) : bool
    {
        $forum = $this->Forum;
        if (!$forum)
        {
            return false;
        }

        return $forum->canChangePostOwner($newOwner, $error);
    }

    /**
     * @param null $error
     *
     * @return bool
     */
    public function canChangePostDate(&$error = null) : bool
    {
        $forum = $this->Forum;
        if (!$forum)
        {
            return false;
        }

        return $forum->canChangePostDate($error);
    }
}