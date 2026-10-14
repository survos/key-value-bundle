<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Menu;

use Survos\KeyValueBundle\Entity\KeyValue;
use Survos\MenuBundle\Attribute\AsMenuListener;
use Survos\MenuBundle\Event\MenuEvent;
use Survos\MenuBundle\MenuSlot;
use Survos\TablerBundle\Menu\AbstractAdminMenuSubscriber;

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

    #[AsMenuListener(MenuSlot::ADMIN_NAVBAR_MENU)]
    public function onAdminNavbarMenu(MenuEvent $event): void
    {
        $this->buildAdminMenu($event);
    }
}
