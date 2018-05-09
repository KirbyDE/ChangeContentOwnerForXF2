<?php

namespace TickTackk\ChangeContentOwner\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Class Post
 *
 * @package TickTackk\ChangeContentOwner
 */
class Post extends XFCP_Post
{
    /**
     * @param ParameterBag $params
     *
     * @return \XF\Mvc\Reply\Error|\XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     *
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionChangeAuthor(ParameterBag $params)
    {
        /** @var \TickTackk\ChangeContentOwner\XF\Entity\Post $post */
        /** @noinspection PhpUndefinedFieldInspection */
        $post = $this->assertViewablePost($params->post_id);
        if (!$post->canChangeAuthor($error))
        {
            return $this->noPermission($error);
        }

        $thread = $post->Thread;

        if ($this->isPost())
        {
            $newAuthor = $this->em()->findOne('XF:User', ['username' => $this->filter('new_author_username', 'str')]);
            if (!$newAuthor)
            {
                return $this->error(\XF::phrase('requested_user_not_found'));
            }

            /** @var \TickTackk\ChangeContentOwner\XF\Service\Post\AuthorChanger $authorChangerService */
            $authorChangerService = $this->service('TickTackk\ChangeContentOwner\XF:Post\AuthorChanger', $post, $newAuthor);
            $authorChangerService->changeAuthor();
            if (!$authorChangerService->validate($errors))
            {
                return $this->error($errors);
            }
            $post = $authorChangerService->save();

            return $this->redirect($this->buildLink('posts', $post));
        }
        else
        {
            /** @var \XF\Entity\Forum $forum */
            $forum = $post->Thread->Forum;

            $viewParams = [
                'post' => $post,
                'thread' => $thread,
                'forum' => $forum
            ];
            return $this->view('TickTackk\ChangeContentOwner\XF:Post\ChangeAuthor', 'changeContentOwner_post_change_author', $viewParams);
        }
    }
}