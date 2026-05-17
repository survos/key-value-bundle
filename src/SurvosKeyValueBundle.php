<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle;

use Survos\KeyValueBundle\Command\KeyValueAdd;
use Survos\KeyValueBundle\Command\KeyValueRemove;
use Survos\KeyValueBundle\Command\KeyValueShow;
use Survos\KeyValueBundle\Entity\KeyValueManager;
use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Survos\KeyValueBundle\Type\DefaultType;
use Survos\KeyValueBundle\Type\EmailType;
use Survos\KeyValueBundle\Type\IpType;
use Survos\KeyValueBundle\Utils\TypeExtractor;
use Survos\KeyValueBundle\Utils\TypeExtractorInterface;
use Survos\KeyValueBundle\Validator\Constraints\IsNotBlacklistedValidator;
use Survos\KeyValueBundle\Validator\Constraints\IsNotKeyValueedValidator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SurvosKeyValueBundle extends AbstractBundle implements CompilerPassInterface
{
    public const SERVICE_TAG = 'survos.key-value.type';

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        foreach ([
            KeyValueAdd::class,
            KeyValueRemove::class,
            KeyValueShow::class,
        ] as $commandName) {
            $builder->autowire($commandName)
                ->setAutoconfigured(true)
                ->addTag('console.command');
        }

        $builder->autowire(KeyValueManagerInterface::class, KeyValueManager::class)
            ->setPublic(true)
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->setArgument('$config', $config)
            ->setArgument('$defaultList', $config['default_list']);

        foreach ([
            DefaultType::class,
            EmailType::class,
            IpType::class,
        ] as $class) {
            $builder->autowire($class)
                ->setPublic(true)
                ->setAutoconfigured(true)
                ->setAutowired(true)
                ->addTag(self::SERVICE_TAG, [
                    'class' => $class,
                ]);
        }

        $builder->autowire(TypeExtractorInterface::class, TypeExtractor::class)
            ->setPublic(false)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setArgument('$types', new TaggedIteratorArgument(self::SERVICE_TAG))
            ->setArgument('$defaultType', $builder->getDefinition($config['default_type']));

        foreach ([
            IsNotKeyValueedValidator::class,
            IsNotBlacklistedValidator::class,
        ] as $validatorClass) {
            $builder->autowire($validatorClass)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->addTag('validator.constraint_validator');
        }
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass($this);
    }


    public function process(ContainerBuilder $container): void
    {
        $container->findTaggedServiceIds(self::SERVICE_TAG);
    }

    private function addListsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
            ->arrayNode('lists')
            ->arrayPrototype()
            ->children()
            ->scalarNode('name')->info('the list key, e.g. banned_ip')->isRequired()->end()
            ->booleanNode('case')->info('if lookups are case-sensitive')->defaultTrue()->end()
            ->scalarNode('regex')->info('validate via regex when adding values')->end()
            ->scalarNode('type')->info('defined validation types, url, email, ip')->end()
            ->end()
            ->end()
            ->end();
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $rootNode = $definition->rootNode();
        $rootNode
            ->children()
            ->scalarNode('default_type')->cannotBeEmpty()->defaultValue(DefaultType::class)->end()
            ->scalarNode('api_key')->info('@todo: integrate with BotPathServer')->defaultNull()->end()
            ->scalarNode('default_list')->defaultValue(null)->end()
            ->booleanNode('strict')->defaultValue(true)->end()
            ->end();

        $this->addListsSection($rootNode);
    }
}
