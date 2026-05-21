<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Menu;

use Survos\KeyValueBundle\Entity\KeyValue;
use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Menu\AbstractAdminMenuSubscriber;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class KeyValueMenuSubscriber extends AbstractAdminMenuSubscriber
{
    protected function getLabel(): string
    {
        return 'Key Value';
    }

    protected function getGroupIcon(): ?string
    {
        return 'mdi:format-list-bulleted';
    }

    protected function getResourceClasses(): array
    {
        return ['Key Values' => KeyValue::class];
    }

    #[AsEventListener(event: MenuEvent::ADMIN_NAVBAR_MENU)]
    public function onAdminNavbarMenu(MenuEvent $event): void
    {
        $this->buildAdminMenu($event);
    }
}
