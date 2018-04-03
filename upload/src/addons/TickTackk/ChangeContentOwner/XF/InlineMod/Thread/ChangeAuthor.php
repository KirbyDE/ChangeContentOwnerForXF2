<?php

namespace TickTackk\ChangeContentOwner\XF\InlineMod\Thread;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Controller;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

class ChangeAuthor extends AbstractAction
{
    /**
     * @return \XF\Phrase
     */
    public function getTitle()
    {
        return \XF::phrase('changeContentOwner_change_thread_author...');
    }

    /**
     * @return array
     */
    public function getBaseOptions()
    {
        return [
            'new_author_username' => null
        ];
    }

    /**
     * @param AbstractCollection $entities
     * @param Controller         $controller
     *
     * @return \XF\Mvc\Reply\View
     */
    public function renderForm(AbstractCollection $entities, Controller $controller)
    {
        $viewParams = [
            'threads' => $entities,
            'total' => count($entities)
        ];
        return $controller->view('XF:Public:InlineMod\Thread\ChangeAuthor', 'inline_mod_thread_change_author', $viewParams);
    }

    /**
     * @param AbstractCollection $entities
     * @param Request            $request
     *
     * @return array
     */
    public function getFormOptions(AbstractCollection $entities, Request $request)
    {
        return [
            'new_author_username' => $request->filter('new_author_username', 'str')
        ];
    }

    /**
     * @param Entity $entity
     * @param array  $options
     * @param null   $error
     *
     * @return bool
     */
    protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
    {
        /** @var \TickTackk\ChangeContentOwner\XF\Entity\Thread $entity */
        return $entity->canChangeAuthor($error);
    }

    /**
     * @param Entity $entity
     * @param array  $options
     */
    protected function applyToEntity(Entity $entity, array $options)
    {
        /** @var \TickTackk\ChangeContentOwner\XF\Entity\Thread $entity */
        if ($entity->discussion_type == 'redirect')
        {
            return;
        }

        $newAuthor = $this->app()->em()->findOne('XF:User', ['username' => $options['new_author_username']]);
        if (!$newAuthor)
        {
            return;
        }

        /** @var \TickTackk\ChangeContentOwner\XF\Service\Thread\AuthorChanger $authorChanger */
        $authorChanger = $this->app()->service('TickTackk\ChangeContentOwner\XF:Thread\AuthorChanger', $entity, $newAuthor);
        $authorChanger->setPerformValidations(false);
        $authorChanger->changeAuthor();
        if ($authorChanger->validate($errors))
        {
            $authorChanger->save();
        }
    }
}