<?php

/*
 * This file is part of the Ivory Base64 File package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Base64FileBundle\Tests\DependencyInjection;

use Ivory\Base64FileBundle\DependencyInjection\IvoryBase64FileExtension;
use Ivory\Base64FileBundle\Form\Extension\Base64FileExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractIvoryBase64FileExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = new ContainerBuilder();

        $this->container->registerExtension($extension = new IvoryBase64FileExtension());
        $this->container->loadFromExtension($extension->getAlias());
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    public function testDefaultForm()
    {
        $this->container->compile();

        $this->assertForm(false);
    }

    public function testEnabledForm()
    {
        $this->loadConfiguration($this->container, 'enabled');
        $this->container->compile();

        $this->assertForm(true);
    }

    /**
     * @param bool $enabled
     */
    private function assertForm($enabled)
    {
        $this->assertTrue($this->container->has($extension = 'ivory.base64_file.form.extension'));
        $this->assertSame($enabled, $this->container->getDefinition($extension)->getArgument(0));

        $this->assertInstanceOf(Base64FileExtension::class, $this->container->get($extension));
        $tag = $this->container->getDefinition($extension)->getTag('form.type_extension');

        if (method_exists(AbstractType::class, 'getBlockPrefix')) {
            $this->assertSame([[
                'extended_type' => FileType::class,
                'extended-type' => FileType::class,
            ]], $tag);
        } else {
            $this->assertSame([['alias' => 'file']], $tag);
        }
    }
}
