<?php


namespace AngularIntegration\Bundle\CoreBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\DBAL\Types\Type;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use AngularIntegration\Bundle\CoreBundle\Generator\ContextGenerator;
use Sensio\Bundle\GeneratorBundle\Command\Validators;

/**
 *
 * @author Thiago Rossetto
 */
class GenerateContextCommand extends GeneratorCommand
{
    protected function configure()
    {
        $this
            ->setName('angularjs:generate:context')
            ->setAliases(array('generate:doctrine:entity'))
            ->setDescription('Generates a new bundle context')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('fields', null, InputOption::VALUE_REQUIRED, 'The fields to create with the new entity')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)', 'annotation');
            //->addOption('with-repository', null, InputOption::VALUE_NONE, 'Whether to generate the entity repository or not');
    }

    /**
     * @throws \InvalidArgumentException When the bundle doesn't end with Bundle (Example: "Bundle/MySampleBundle")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Você confirma a criação deste contexto', 'yes', '?'), true)) {
                $output->writeln('<error>Comando abortado</error>');

                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);
        $format = Validators::validateFormat($input->getOption('format'));
        $fields = $this->parseFields($input->getOption('fields'));



        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        $generator = $this->getGenerator();

        $generator->generate($bundle, $entity, $format, array_values($fields), array());

        $dialog->writeSection($output, 'Parabéns, contexto criado com sucesso!!! Fácil e rápido, não?! :)');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Bem vindo ao gerador de módulo');

        // namespace
        $output->writeln(array(
            '',
            'Este comando ajuda você gerar um novo contexto.',
            '',
            'Deve-se fazer da seguinte forma <comment>AngularIntegration(Nome_do_Bundle)Bundle:(Nome_do_Contexto)</comment>.',
            ''
        ));

        $bundleNames = array_keys($this->getContainer()->get('kernel')->getBundles());

        while (true) {
            $context = $dialog->askAndValidate($output, $dialog->getQuestion('Nome do contexto', $input->getOption('entity')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'), $bundleNames);

            list($bundle, $context) = $this->parseShortcutNotation($context);

            // check reserved words
            if ($this->getGenerator()->isReservedKeyword($context)){
                $output->writeln(sprintf('<bg=red> "%s" is a reserved word</>.', $context));
                continue;
            }

            try {
                $b = $this->getContainer()->get('kernel')->getBundle($bundle);

                if (!file_exists($b->getPath().'/Entity/'.str_replace('\\', '/', $context).'.php')) {
                    break;
                }

                $output->writeln(sprintf('<bg=red>Entity "%s:%s" already exists</>.', $bundle, $context));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }
        }
        $input->setOption('entity', $bundle.':'.$context);

        // format
        $output->writeln(array(
            '',
            'Determine o formato para utilizar o mapeamento das informações (recomendado: annotation).',
            '',
        ));

        $formats = array('yml', 'xml', 'php', 'annotation');

        $format = $dialog->askAndValidate($output, $dialog->getQuestion('Formato para configuração (yml, xml, php, ou annotation)', $input->getOption('format')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'), false, $input->getOption('format'), $formats);
        $input->setOption('format', $format);

        // fields
        $input->setOption('fields', $this->addFields($input, $output, $dialog));

        $output->writeln('');

    }

    private function parseFields($input)
    {
        if (is_array($input)) {
            return $input;
        }

        $fields = array();
        foreach (explode(' ', $input) as $value) {
            $elements = explode(':', $value);
            $name = $elements[0];
            if (strlen($name)) {
                $type = isset($elements[1]) ? $elements[1] : 'string';
                preg_match_all('/(.*)\((.*)\)/', $type, $matches);
                $type = isset($matches[1][0]) ? $matches[1][0] : $type;
                $length = isset($matches[2][0]) ? $matches[2][0] : null;

                $fields[$name] = array('fieldName' => $name, 'type' => $type, 'length' => $length);
            }
        }

        return $fields;
    }

    private function addFields(InputInterface $input, OutputInterface $output, DialogHelper $dialog)
    {
        $fields = $this->parseFields($input->getOption('fields'));
        $output->writeln(array(
            '',
            'A chave primária é adicionada automaticamente (<comment>id</comment>).',
            '',
        ));
        $output->write('<info>Tipos permitidos:</info> ');

        $types = array_keys(Type::getTypesMap());
        $count = 20;
        foreach ($types as $i => $type) {
            if ($count > 50) {
                $count = 0;
                $output->writeln('');
            }
            $count += strlen($type);
            $output->write(sprintf('<comment>%s</comment>', $type));
            if (count($types) != $i + 1) {
                $output->write(', ');
            } else {
                $output->write('.');
            }
        }
        $output->writeln('');

        $fieldValidator = function ($type) use ($types) {
            // FIXME: take into account user-defined field types
            if (!in_array($type, $types)) {
                throw new \InvalidArgumentException(sprintf('Invalid type "%s".', $type));
            }

            return $type;
        };

        $lengthValidator = function ($length) {
            if (!$length) {
                return $length;
            }

            $result = filter_var($length, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Invalid length "%s".', $length));
            }

            return $length;
        };

        while (true) {
            $output->writeln('');
            $generator = $this->getGenerator();
            $columnName = $dialog->askAndValidate($output, $dialog->getQuestion('Novo campo (Pressione "Enter" para parar de adicionar campos)', null), function ($name) use ($fields, $generator) {
                if (isset($fields[$name]) || 'id' == $name) {
                    throw new \InvalidArgumentException(sprintf('Field "%s" is already defined.', $name));
                }

                // check reserved words
                if ($generator->isReservedKeyword($name)){
                    throw new \InvalidArgumentException(sprintf('Name "%s" is a reserved word.', $name));
                }

                return $name;
            });
            if (!$columnName) {
                break;
            }

            $defaultType = 'string';

            // try to guess the type by the column name prefix/suffix
            if (substr($columnName, -3) == '_at') {
                $defaultType = 'datetime';
            } elseif (substr($columnName, -3) == '_id') {
                $defaultType = 'integer';
            } elseif (substr($columnName, 0, 3) == 'is_') {
                $defaultType = 'boolean';
            } elseif (substr($columnName, 0, 4) == 'has_') {
                $defaultType = 'boolean';
            }

            $type = $dialog->askAndValidate($output, $dialog->getQuestion('Field type', $defaultType), $fieldValidator, false, $defaultType, $types);

            $data = array('columnName' => $columnName, 'fieldName' => lcfirst(Container::camelize($columnName)), 'type' => $type);

            if ($type == 'string') {
                $data['length'] = $dialog->askAndValidate($output, $dialog->getQuestion('Field length', 255), $lengthValidator, false, 255);
            }

            $fields[$columnName] = $data;
        }

        return $fields;
    }

    protected function createGenerator()
    {
        return new ContextGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('doctrine'));
    }

    public function isEnabled()
    {
        return class_exists('Doctrine\\Bundle\\DoctrineBundle\\DoctrineBundle');
    }

    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    protected function getEntityMetadata($entity)
    {
        $factory = new MetadataFactory($this->getContainer()->get('doctrine'));

        return $factory->getClassMetadata($entity)->getMetadata();
    }


}
