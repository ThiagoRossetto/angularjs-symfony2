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

       /* $this->renderFile('AppController.php.twig', $dir.'/Service/SampleService.php', $parameters);
        $this->renderFile('AppController.php.twig', $dir.'/Entity/Sample.php', $parameters);
        $this->renderFile('AppController.php.twig', $dir.'/Repository/SampleRepository.php', $parameters);
       */

        $this->renderFile('routing.yml.twig', $dir.'/Resources/config/routing.yml', $parameters);

        $this->renderFile('index.html.twig', $dir.'/Resources/views/Default/index.html.twig', $parameters);
        $this->renderFile('scripts.html.twig', $dir.'/Resources/views/Default/scripts.html.twig', $parameters);
        $this->renderFile('root-bundle-main.js.twig', $dir.'/Resources/public/js/root-bundle-main.js', $parameters);
       /*
        $this->renderFile('detail.html.twig', $dir.'/Resources/public/templates/sample/sample-detail.html', $parameters);
        $this->renderFile('form.html.twig', $dir.'/Resources/public/templates/sample/sample-form.html', $parameters);
        $this->renderFile('list.html.twig', $dir.'/Resources/public/templates/sample/sample-list.html', $parameters);
        $this->renderFile('view.html.twig', $dir.'/Resources/public/templates/sample/sample-view.html', $parameters);
        $this->renderFile('Sample.js.twig', $dir.'/Resources/public/js/entity/Sample.js', $parameters);

        $this->renderFile('controllers.js.twig', $dir.'/Resources/public/js/sample-controller.js', $parameters);
       */
        //$this->renderFile('services.js.twig', $dir.'/Resources/public/js/services.js', $parameters);
        //$this->renderFile('angular.min.js', $dir.'/Resources/public/lib/angular/angular.min.js', $parameters);
        //$this->renderFile('angular-route.min.js', $dir.'/Resources/public/lib/angular/angular-route.min.js', $parameters);
        //$this->renderFile('jquery-1.10.2.min.js', $dir.'/Resources/public/lib/jquery/jquery-1.10.2.min.js', $parameters);

        if ('xml' === $format || 'annotation' === $format) {
            $this->renderFile('services.xml.twig', $dir.'/Resources/config/services.xml', $parameters);
        } else {
            $this->renderFile('services.'.$format.'.twig', $dir.'/Resources/config/services.'.$format, $parameters);
        }

        if ('annotation' != $format) {
            $this->renderFile('routing.'.$format.'.twig', $dir.'/Resources/config/routing.'.$format, $parameters);
        }

        /*if ($structure) {
            $this->renderFile('bundle/messages.fr.xlf', $dir.'/Resources/translations/messages.fr.xlf', $parameters);

            $this->filesystem->mkdir($dir.'/Resources/doc');
            $this->filesystem->touch($dir.'/Resources/doc/index.rst');
            $this->filesystem->mkdir($dir.'/Resources/translations');
            $this->filesystem->mkdir($dir.'/Resources/public/css');
            $this->filesystem->mkdir($dir.'/Resources/public/images');
            $this->filesystem->mkdir($dir.'/Resources/public/js');
        }*/
    }

    /*public function generatew($namespace, $bundle, $dir, $format, $structure)
    {
        $dir .= '/'.strtr($namespace, '\\', '/');

        if (file_exists($dir)){
            if (!is_dir($dir))
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($dir)));

            $files = scandir($dir);
            if ($files != array('.', '..'))
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));

            if (!is_writable($dir))
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
        }

        $basename = substr($bundle, 0, -6);
        $moduleName =  substr($basename, 7);
        $underscoreBasename = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $moduleName));
        $uidBasename = strtoupper(preg_replace('/(.)([A-Z])/', '$1_$2', $moduleName));
        $humanBasename = preg_replace('/(.)([A-Z])/', '$1 $2', $moduleName);

        $basename = substr($bundle, 0, -6);
        $parameters = array(
            'namespace' => $namespace,
            'bundle'    => $bundle,
            'format'    => $format,
            'bundle_basename' => $basename,
            'extension_alias' => Container::underscore($basename),
        );



        //$this->renderFile($this->skeletonDir, 'Bundle.php.twig', $dir.'/'.$bundle.'.php', $parameters);
        //$this->renderFile($this->skeletonDir, 'Extension.php.twig', $dir.'/DependencyInjection/'.$basename.'Extension.php', $parameters);
        //$this->renderFile($this->skeletonDir, 'Configuration.php.twig', $dir.'/DependencyInjection/Configuration.php', $parameters);
        $this->renderFile($this->skeletonDir, 'AppController.php.twig', $dir.'/Controller/AppController.php', $parameters);
        //$this->renderFile($this->skeletonDir, 'ExampleControllerTest.php.twig', $dir.'/Tests/Controller/ExampleControllerTest.php', $parameters);
        //$this->renderFile($this->skeletonDir, 'services.yml.twig', $dir.'/Resources/config/services.yml', $parameters);
        $this->renderFile($this->skeletonDir, 'routing.yml.twig', $dir.'/Resources/config/routing.yml', $parameters);
        $this->renderFile($this->skeletonDir, 'index.html.twig', $dir.'/Resources/views/App/index.html.twig', $parameters);
        $this->renderFile($this->skeletonDir, 'header.html.twig', $dir.'/Resources/views/App/header.html.twig', $parameters);
        $this->renderFile($this->skeletonDir, 'footer.html.twig', $dir.'/Resources/views/App/footer.html.twig', $parameters);
        $this->renderFile($this->skeletonDir, 'home.html.twig', $dir.'/Resources/views/Partials/home.html.twig', $parameters);
        $this->renderFile($this->skeletonDir, 'app.js.twig', $dir.'/Resources/public/js/app.js', $parameters);
        $this->renderFile($this->skeletonDir, 'controller.js.twig', $dir.'/Resources/public/js/controller.js', $parameters);
        $this->renderFile($this->skeletonDir, 'services.js.twig', $dir.'/Resources/public/js/services.js', $parameters);
        $this->renderFile($this->skeletonDir, 'angular.min.js', $dir.'/Resources/public/lib/angular/angular.min.js', $parameters);
        $this->renderFile($this->skeletonDir, 'angular-route.min.js', $dir.'/Resources/public/lib/angular/angular-route.min.js', $parameters);
        $this->renderFile($this->skeletonDir, 'jquery-1.10.2.min.js', $dir.'/Resources/public/lib/jquery/jquery-1.10.2.min.js', $parameters);
        //$this->filesystem->copy( $this->skeletonDir .'/dependency.json' , $dir.'/Resources/public/config/dependency.json' );

        /*$this->filesystem->mkdir($dir.'/Resources/doc');
        $this->filesystem->touch($dir.'/Resources/doc/index.rst');
        $this->filesystem->mkdir($dir.'/Resources/translations');
        $this->filesystem->touch($dir.'/Resources/translations/messages.pt_BR.yml');
        $this->filesystem->mkdir($dir.'/Resources/public/css');
        $this->filesystem->touch($dir.'/Resources/public/css/style.css');
        $this->filesystem->mkdir($dir.'/Resources/public/config');


    }*/
} 