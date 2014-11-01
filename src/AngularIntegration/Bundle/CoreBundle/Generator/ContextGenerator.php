<?php

namespace AngularIntegration\Bundle\CoreBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

class ContextGenerator extends Generator
{
    private $filesystem;
    private $registry;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, RegistryInterface $registry)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
        $this->skeletonDir = dirname(__FILE__). "/../Resources/skeleton/context/";
    }

    public function generate(BundleInterface $bundle, $entity, $format, array $fields, $withRepository)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
            array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
            $config->getEntityNamespaces()
        ));


        $entityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$entity;
        $entityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass);
       // if ($withRepository) {
            $class->customRepositoryClassName = str_replace("\\Entity\\","\\Repository\\",$entityClass).'Repository';
        //}
        $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
        foreach ($fields as $field) {
            $class->mapField($field);
        }

        $entityGenerator = $this->getEntityGenerator();
        if ('annotation' === $format) {
            $entityGenerator->setGenerateAnnotations(true);
            $entityCode = $entityGenerator->generateEntityClass($class);
            $mappingPath = $mappingCode = false;
        } else {
            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $entity).'.orm.'.$format;

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
            }

            $mappingCode = $exporter->exportClassMetadata($class);
            $entityGenerator->setGenerateAnnotations(false);
            $entityCode = $entityGenerator->generateEntityClass($class);
        }

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
        }

        if ($withRepository) {
            $path = $bundle->getPath().str_repeat('/..', substr_count(get_class($bundle), '\\'));
            $this->getRepositoryGenerator()->writeEntityRepositoryClass($class->customRepositoryClassName, $path);
        }


        $basename = substr($bundle->getName(), 0, -6);

        $bundleName = explode('\\',$bundle->getNamespace());
        $bundleName = $bundleName[count($bundleName)-1];
        $bundleName = str_replace('Bundle','',$bundleName);

        $parameters = array(
            'namespace' => $bundle->getNamespace(),
            'bundle'    => $bundle->getName(),
            'format'    => $format,
            'bundle_name'=> strtolower($bundleName),
            'bundle_name_ecfirst'=>$bundleName,
            'bundle_basename' => $basename,
            'bundle_basename_tolower'=> strtolower($basename),
            'context' => $entity,
            'fieldMappings' => $class->fieldMappings
        );

        $this->setSkeletonDirs($this->skeletonDir);

        /*Service*/
        $this->renderFile('service.php.twig', $bundle->getPath().'/Service/'.$entity.'Service.php', $parameters);

        /*Repository*/
        $this->renderFile('repository.php.twig', $bundle->getPath().'/Repository/'.$entity.'Repository.php', $parameters);

        /*Controller js*/
        $this->renderFile('controller.js.twig', $bundle->getPath().'/Resources/public/js/'.strtolower($entity).'-controller.js', $parameters);

        /*Entity js*/
        $this->renderFile('entity.js.twig', $bundle->getPath().'/Resources/public/js/entity/'.$entity.'.js', $parameters);

        /*HTML*/
        $this->renderFile('detail.html.twig', $bundle->getPath().'/Resources/public/templates/'.strtolower($entity).'/'.strtolower($entity).'-detail.html', $parameters);
        $this->renderFile('form.html.twig', $bundle->getPath().'/Resources/public/templates/'.strtolower($entity).'/'.strtolower($entity).'-form.html', $parameters);
        $this->renderFile('list.html.twig', $bundle->getPath().'/Resources/public/templates/'.strtolower($entity).'/'.strtolower($entity).'-list.html', $parameters);
        $this->renderFile('view.html.twig', $bundle->getPath().'/Resources/public/templates/'.strtolower($entity).'/'.strtolower($entity).'-view.html', $parameters);

        file_put_contents($bundle->getPath().'/Resources/views/Default/scripts.html.twig',"\n".'<script type="text/javascript" src="resource/'.strtolower($bundleName).'?file=js/entity/'.$entity.'.js"></script>', FILE_APPEND);
        file_put_contents($bundle->getPath().'/Resources/views/Default/scripts.html.twig',"\n".'<script type="text/javascript" src="resource/'.strtolower($bundleName).'?file=js/'.strtolower($entity).'-controller.js"></script>', FILE_APPEND);


        $this->updateRootControllerJs($bundle, $entity);

    }

    private function updateRootControllerJs($bundle, $entityName) {
        $entityNameLower = strtolower($entityName);
        $bundleNameLower = substr(strtolower($bundle->getName()), 0, -6);

        $path = $bundle->getPath()."/Resources/public/js/root-bundle-main.js.bkp";
        $handle = @fopen($bundle->getPath()."/Resources/public/js/root-bundle-main.js", "r");
        file_put_contents($path,'');
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, 4096);
                file_put_contents($path,$buffer,FILE_APPEND);
                if(strpos($buffer, '//@beginStates') !== false ) {
                    $file = <<<STATE
        \$stateProvider.state('$entityNameLower', {
                url : "/$entityNameLower",
                templateUrl : "bundles/$bundleNameLower/templates/$entityNameLower/$entityNameLower-view.html",
                controller : {$entityName}Controller
            })
                .state('$entityNameLower.detalhe', {
                    url: "/detalhe/:id"
                })
                .state('$entityNameLower.listar', {
                    url: "/listar"
                })
                .state('$entityNameLower.criar', {
                    url : "/criar"
                })
                .state('$entityNameLower.editar', {
                    url : "/editar/:id"
                });
STATE;


                    file_put_contents($path,"\n".$file."\n",FILE_APPEND);
                }
            }
            fclose($handle);
            unlink($bundle->getPath()."/Resources/public/js/root-bundle-main.js");
            rename($path, $bundle->getPath()."/Resources/public/js/root-bundle-main.js");
        }

    }

    public function isReservedKeyword($keyword)
    {
        return $this->registry->getConnection()->getDatabasePlatform()->getReservedKeywordsList()->isKeyword($keyword);
    }

    protected function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setClassToExtend('AngularIntegration\Bundle\CoreBundle\Entity\AbstractEntity');

        /* Gambiarra master -- TODO: Verificar outro meio de colocar public - ou arrumar no amf */
        $entityGenerator->setFieldVisibility('public');

        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

    protected function getRepositoryGenerator()
    {
        return new EntityRepositoryGenerator();
    }
}
