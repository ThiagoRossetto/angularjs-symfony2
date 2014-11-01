<?php

namespace AngularIntegration\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\KernelInterface;
//use Sensio\Bundle\GeneratorBundle\Generator\BundleGenerator;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use AngularIntegration\Bundle\CoreBundle\Generator\BundleGenerator;

/*
 * TODO:
 * Achar uma maneira do usuário escolher o nome do "vendor", sem depender do AngularIntegration
 * Implementar DDD
 *
 * */
class InstallAngularCommand extends GeneratorCommand
{
    /*
     * @Method configure
     * - Responsável pela configuração do comando que será executado via terminal
     * */
    protected function configure()
    {
        /*
         * @Method: setDefinition - Definições das opções que serão apresentadas na criação do módulo;
         * @Method: setDescription - Definir a descrição que aparecerá após executar o comando "php app/console";
         * @Method: setName - Definir o nome do comando para gerar a ação desejada.
         * Exemplo: "php app/console angularjs:generate:bundle" -> Irá executar o método execute.
         * */
        $this
            ->setDefinition(array(
                new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create'),
                new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The optional bundle name'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)'),
                new InputOption('structure', '', InputOption::VALUE_NONE, 'Whether to generate the whole directory structure'),
                new InputOption('InternalArchitecture', '', InputOption::VALUE_NONE, 'Insert the parent bundle'),
            ))
            ->setDescription('Generates a angular bundle.')
            ->setName('angularjs:generate:bundle')
        ;
    }

    /*
     * @Method execute
     * - Será executado após executar o comando definido no método "configure" via termianal.
     * */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        foreach (array('namespace', 'dir') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
        if (!$bundle = $input->getOption('bundle-name')) {
            $bundle = strtr($namespace, array('\\' => ''));
        }
        $bundle = Validators::validateBundleName($bundle);
        $dir = Validators::validateTargetDir($input->getOption('dir'), $bundle, $namespace);
        if (null === $input->getOption('format')) {
            $input->setOption('format', 'annotation');
        }
        $format = Validators::validateFormat($input->getOption('format'));
        $structure = $input->getOption('structure');

        //$dialog->writeSection($output, 'Bundle generation');

        if (!$this->getContainer()->get('filesystem')->isAbsolutePath($dir)) {
            $dir = getcwd().'/'.$dir;
        }

        $generator = $this->getGenerator();
        $generator->generate($namespace, $bundle, $dir, $format, $structure);

        $output->writeln('Generating the bundle code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // check that the namespace is already autoloaded
        $runner($this->checkAutoloader($output, $namespace, $bundle, $dir));

        // register the bundle in the Kernel class
        $runner($this->updateKernel($dialog, $input, $output, $this->getContainer()->get('kernel'), $namespace, $bundle));

        $this->writeGeneratorSummary($output, $errors);
    }

    public function writeGeneratorSummary(OutputInterface $output, $errors)
    {
        $dialog = $this->getDialogHelper();
        if (!$errors) {
            $dialog->writeSection($output, 'Módulo criado com sucesso!');
        } else {
            $dialog->writeSection($output, array(
                'The command was not able to configure everything automatically.',
                'You must do the following changes manually.',
            ), 'error');

            $output->writeln($errors);
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        echo "interact";
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Bem vindo ao gerador de módulo com integração AngularJS');

        // namespace
        $namespace = null;
        try {
            $namespace = $input->getOption('namespace') ? Validators::validateBundleNamespace($input->getOption('namespace')) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $namespace) {
            $output->writeln(array(
                '',
                'Este comando evitará a fagida na criação de um novo <comment>módulo</comment>.',
                'Além disso, fornecerá uma estrutura completa de pastas com o angularJS embutido.',
                '',
                'O padrão de criação é igual ao do Symfony2 (<comment>Acme/Bundle/BlogBundle</comment>).',
                'Para utilizar os recursos do CORE, o nome do "vendor" precisará ser <comment>AngularIntegration</comment>.',
                '',
                'Nesta versão, é obrigatório começar com (<comment>AngularIntegration/Bundle</comment>)',
                '',
                'Utilize <comment>/</comment> como delimitador.',
                '',
            ));

            $namespace = $dialog->askAndValidate($output, $dialog->getQuestion('Namespace do bundle', $input->getOption('namespace')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'), false, $input->getOption('namespace'));
            $input->setOption('namespace', $namespace);
        }

        // bundle name
        $bundle = null;
        try {
            $bundle = $input->getOption('bundle-name') ? Validators::validateBundleName($input->getOption('bundle-name')) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $bundle) {
            $bundle = strtr($namespace, array('\\Bundle\\' => '', '\\' => ''));
            $input->setOption('bundle-name', $bundle);
        }

        // target dir
        $dir = null;
        try {
            $dir = $input->getOption('dir') ? Validators::validateTargetDir($input->getOption('dir'), $bundle, $namespace) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $dir) {
            $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src';
            $input->setOption('dir', $dir);
        }

        // format
        $format = null;
        try {
            $format = $input->getOption('format') ? Validators::validateFormat($input->getOption('format')) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $format) {
            $output->writeln(array(
                '',
                'Determine o formato a ser usado para a configuração gerada',
                '',
            ));
            $format = $dialog->askAndValidate($output, $dialog->getQuestion('Formato de configuração (yml, xml, php, ou annotation)', $input->getOption('format')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'), false, $input->getOption('format'));
            $input->setOption('format', $format);
        }

        $structure = $input->getOption('structure');
        $input->setOption('structure', $structure);

    }

    protected function checkAutoloader(OutputInterface $output, $namespace, $bundle, $dir)
    {
        $output->write('Checking that the bundle is autoloaded: ');
        if (!class_exists($namespace.'\\'.$bundle)) {
            return array(
                '- Edit the <comment>composer.json</comment> file and register the bundle',
                '  namespace in the "autoload" section:',
                '',
            );
        }
    }

    protected function updateKernel(DialogHelper $dialog, InputInterface $input, OutputInterface $output, KernelInterface $kernel, $namespace, $bundle)
    {
        $auto = true;
        /*if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of your Kernel', 'yes', '?'), true);
        }*/

        $output->write('Enabling the bundle inside the Kernel: ');
        $manip = new KernelManipulator($kernel);
        try {
            $ret = $auto ? $manip->addBundle($namespace.'\\'.$bundle) : false;

            if (!$ret) {
                $reflected = new \ReflectionObject($kernel);

                return array(
                    sprintf('- Edit <comment>%s</comment>', $reflected->getFilename()),
                    '  and add the following bundle in the <comment>AppKernel::registerBundles()</comment> method:',
                    '',
                    sprintf('    <comment>new %s(),</comment>', $namespace.'\\'.$bundle),
                    '',
                );
            }
        } catch (\RuntimeException $e) {
            return array(
                sprintf('Bundle <comment>%s</comment> is already defined in <comment>AppKernel::registerBundles()</comment>.', $namespace.'\\'.$bundle),
                '',
            );
        }
    }

    protected function updateRouting(DialogHelper $dialog, InputInterface $input, OutputInterface $output, $bundle, $format)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of the Routing', 'yes', '?'), true);
        }

        $output->write('Importing the bundle routing resource: ');
        $routing = new RoutingManipulator($this->getContainer()->getParameter('kernel.root_dir').'/config/routing.yml');
        try {
            $ret = $auto ? $routing->addResource($bundle, $format) : false;
            if (!$ret) {
                if ('annotation' === $format) {
                    $help = sprintf("        <comment>resource: \"@%s/Controller/\"</comment>\n        <comment>type:     annotation</comment>\n", $bundle);
                } else {
                    $help = sprintf("        <comment>resource: \"@%s/Resources/config/routing.%s\"</comment>\n", $bundle, $format);
                }
                $help .= "        <comment>prefix:   /</comment>\n";

                return array(
                    '- Import the bundle\'s routing resource in the app main routing file:',
                    '',
                    sprintf('    <comment>%s:</comment>', $bundle),
                    $help,
                    '',
                );
            }
        } catch (\RuntimeException $e) {
            return array(
                sprintf('Bundle <comment>%s</comment> is already imported.', $bundle),
                '',
            );
        }
    }

    protected function createGenerator()
    {
        return new BundleGenerator($this->getContainer()->get('filesystem'));
    }
}
