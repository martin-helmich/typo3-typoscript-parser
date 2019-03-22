<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class TypoScriptParserExtension
 *
 * @package Helmich\TypoScriptParser
 * @codeCoverageIgnore
 */
class TypoScriptParserExtension implements ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../config'));
        $loader->load('services.yml');
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return void The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return void The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'typoscript_parser';
    }
}
