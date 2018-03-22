<?php

namespace TickTackk\ChangeContentOwner\XFMG\InlineMod\Media;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Controller;

class ChangeAuthor extends AbstractAction
{
    /**
     * @return \XF\Phrase
     */
    public function getTitle()
    {
        return \XF::phrase('changeContentOwner_change_xfmg_media_author...');
    }

    /**
     * @param Entity $entity
     * @param array $options
     * @param null $error
     * 
     * @return bool
     */
    protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
    {
        /** @var \TickTackk\ChangeContentOwner\XFMG\Entity\MediaItem $entity */
        return $entity->canChangeAuthor($error);
    }

    /**
     * @param Entity $entity
     * @param array $options
     */
    protected function applyToEntity(Entity $entity, array $options)
    {
        /** @var \TickTackk\ChangeContentOwner\XFMG\Entity\MediaItem $entity */
        $newAuthor = $this->app()->em()->findOne('XF:User', ['username' => $options['new_author_username']]);
        if (!$newAuthor)
        {
            return;
        }

        /** @var \TickTackk\ChangeContentOwner\XFMG\Service\MediaItem\AuthorChanger $authorChanger */
        $authorChanger = $this->app()->service('TickTackk\ChangeContentOwner\XFMG:MediaItem\AuthorChanger', $entity, $newAuthor);
        $authorChanger->setPerformValidations(false);
        $authorChanger->changeAuthor();
        if ($authorChanger->validate($errors))
        {
            $authorChanger->save();
        }
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
     * @param Controller $controller
     * 
     * @return \XF\Mvc\Reply\View
     */
    public function renderForm(AbstractCollection $entities, Controller $controller)
    {
        $viewParams = [
            'media_items' => $entities,
            'total' => count($entities)
        ];
        return $controller->view('XFMG:Public:InlineMod\MediaItem\ChangeAuthor', 'inline_mod_xfmg_media_change_author', $viewParams);
    }

    /**
     * @param AbstractCollection $entities
     * @param Request $request
     * 
     * @return array
     */
    public function getFormOptions(AbstractCollection $entities, Request $request)
    {
        return [
            'new_author_username' => $request->filter('new_author_username', 'str')
        ];
    }
}