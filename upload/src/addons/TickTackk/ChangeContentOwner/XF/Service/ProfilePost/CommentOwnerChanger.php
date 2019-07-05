<?php

namespace TickTackk\ChangeContentOwner\XF\Service\ProfilePost;

use TickTackk\ChangeContentOwner\Service\Content\AbstractOwnerChanger;
use TickTackk\ChangeContentOwner\XF\Entity\ProfilePostComment as ExtendedProfilePostCommentEntity;
use XF\Entity\User as UserEntity;
use XF\Mvc\Entity\Entity;

/**
 * Class CommentOwnerChanger
 *
 * @package TickTackk\ChangeContentOwner\XF\Service\ProfilePost
 */
class CommentOwnerChanger extends AbstractOwnerChanger
{
    /**
     * @return string
     */
    protected function getEntityIdentifier(): string
    {
        return 'XF:ProfilePostComment';
    }

    /**
     * @return string
     */
    protected function getRepoIdentifier(): string
    {
        return 'XF:ProfilePost';
    }

    /**
     * @param Entity|ExtendedProfilePostCommentEntity     $content
     * @param UserEntity $newOwner
     *
     * @return Entity
     */
    protected function changeContentOwner(Entity $content, UserEntity $newOwner): Entity
    {
        $content->user_id = $newOwner->user_id;
        $content->username = $newOwner->username;

        return $content;
    }

    /**
     * @param Entity|ExtendedProfilePostCommentEntity $content
     * @param int    $newDate
     *
     * @return Entity
     */
    protected function changeContentDate(Entity $content, int $newDate): Entity
    {
        $oldDate = $this->getOldDate($content);
        $content->comment_date = $newDate;

        $profilePost = $content->ProfilePost;
        if ($profilePost)
        {
            if ($profilePost->first_comment_date === $oldDate)
            {
                $profilePost->first_comment_date = $newDate;
            }

            if ($profilePost->last_comment_date === $oldDate)
            {
                $profilePost->last_comment_date = $newDate;
            }
        }

        return $content;
    }

    /**
     * @param Entity|ExtendedProfilePostCommentEntity $content
     */
    protected function additionalEntitySave(Entity $content): void
    {
    }
}