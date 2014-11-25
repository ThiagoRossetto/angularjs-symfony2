<?php

namespace AngularIntegration\Bundle\CoreBundle\Generator;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;


class BundleGenerator extends Generator
{
    private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = dirname(__FILE__). "/../Resources/skeleton/bundle/";
    }

    public function generate($namespace, $bundle, $dir, $format, $structure)
    {
        $dir .= '/'.strtr($namespace, '\\', '/');
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($dir)));
            }
            $files = scandir($dir);
            if ($files != array('.', '..')) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
            }
        }

        $basename = substr($bundle, 0, -6);

        $bundleName = explode('\\',$namespace);
        $bundleName = $bundleName[count($bundleName)-1];
        $bundleName = str_replace('Bundle','',$bundleName);

        $parameters = array(
            'namespace' => $namespace,
            'bundle'    => $bundle,
            'format'    => $format,
            'bundle_name'=> strtolower($bundleName),
            'bundle_basename' => $basename,
            'bundle_basename_tolower'=> strtolower($basename),
            'extension_alias' => Container::underscore($basename),
        );

        $this->setSkeletonDirs($this->skeletonDir);

        $this->renderFile('Bundle.php.twig', $dir.'/'.$bundle.'.php', $parameters);
        $this->renderFile('Extension.php.twig', $dir.'/DependencyInjection/'.$basename.'Extension.php', $parameters);
        $this->renderFile('Configuration.php.twig', $dir.'/DependencyInjection/Configuration.php', $parameters);
        $this->renderFile('AppController.php.twig', $dir.'/Controller/AppController.php', $parameters);

        $this->renderFile('routing.yml.twig', $dir.'/Resources/config/routing.yml', $parameters);

        $this->renderFile('index.html.twig', $dir.'/Resources/views/Default/index.html.twig', $parameters);
        $this->renderFile('scripts.html.twig', $dir.'/Resources/views/Default/scripts.html.twig', $parameters);
        $this->renderFile('root-bundle-main.js.twig', $dir.'/Resources/public/js/root-bundle-main.js', $parameters);

        if ('xml' === $format || 'annotation' === $format) {
            $this->renderFile('services.xml.twig', $dir.'/Resources/config/services.xml', $parameters);
        } else {
            $this->renderFile('services.'.$format.'.twig', $dir.'/Resources/config/services.'.$format, $parameters);
        }

        if ('annotation' != $format) {
            $this->renderFile('routing.'.$format.'.twig', $dir.'/Resources/config/routing.'.$format, $parameters);
        }

    }

} 