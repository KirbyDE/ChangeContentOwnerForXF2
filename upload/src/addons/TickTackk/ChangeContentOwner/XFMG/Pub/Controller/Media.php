<?php

namespace TickTackk\ChangeContentOwner\XFMG\Pub\Controller;

use TickTackk\ChangeContentOwner\ControllerPlugin\Content as ContentPlugin;
use TickTackk\ChangeContentOwner\Pub\Controller\ContentTrait;
use TickTackk\ChangeContentOwner\Service\Content\EditorInterface as EditorSvcInterface;
use XF\ControllerPlugin\AbstractPlugin;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Error as ErrorReply;
use XF\Mvc\Reply\Exception as ExceptionReply;
use XF\Mvc\Reply\Redirect as RedirectReply;
use XF\Mvc\Reply\View as ViewReply;
use XFMG\Service\Media\Editor as MediaEditorSvc;
use XFMG\Entity\MediaItem as MediaItemEntity;
use TickTackk\ChangeContentOwner\XFMG\Entity\MediaItem as ExtendedMediaItemEntity;

/**
 * Class Media
 *
 * @package TickTackk\ChangeContentOwner\XFMG\Pub\Controller
 */
class Media extends XFCP_Media
{
    use ContentTrait;

    /**
     * @param ParameterBag $parameterBag
     *
     * @return RedirectReply|ViewReply
     * @throws ExceptionReply
     * @throws \XF\Db\Exception
     * @throws \XF\PrintableException
     */
    public function actionChangeOwner(ParameterBag $parameterBag)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $mediaItem = $this->assertViewableMediaItem($parameterBag->media_id);

        return $this->getChangeContentOwnerPlugin()->actionChangeOwner(
            $mediaItem,
            'TickTackk\ChangeContentOwner\XFMG:Media\OwnerChanger',
            'XFMG:MediaItem',
            'TickTackk\ChangeContentOwner\XFMG:Media\ChangeOwner',
            'XFMG:Media'
        );
    }

    /**
     * @param ParameterBag $params
     *
     * @return ErrorReply|RedirectReply|ViewReply
     */
    public function actionEdit(ParameterBag $params)
    {
        $reply = parent::actionEdit($params);

        $this->getChangeContentOwnerPlugin()->extendContentEditAction(
            $reply,
            'mediaItem',
            'XFMG:Media'
        );

        return $reply;
    }

    /**
     * @param MediaItemEntity $mediaItem
     *
     * @return EditorSvcInterface|MediaEditorSvc
     * @throws ExceptionReply
     */
    protected function setupMediaItemEdit(MediaItemEntity $mediaItem)
    {
        /** @var MediaEditorSvc|EditorSvcInterface $editor */
        $editor = parent::setupMediaItemEdit($mediaItem);

        $this->getChangeContentOwnerPlugin()->extendEditorService($mediaItem, $editor, 'XFMG:Media');

        return $editor;
    }
}