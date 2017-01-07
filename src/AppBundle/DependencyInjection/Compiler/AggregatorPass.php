<?php


namespace AppBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AggregatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('aggregator_registry')) {
            return;
        }

        $definition = $container->findDefinition('aggregator_registry');
        $aggregators = $container->findTaggedServiceIds('aggregator');

        foreach ($aggregators as $id => $tags) {

            if (!isset($tags[0]['alias'])) {
                throw new InvalidArgumentException('Required parameter alias not found in the service definition.');
            }

            $alias = $tags[0]['alias'];

            $definition->addMethodCall('register', [new Reference($id), $alias]);
        }
    }
}
