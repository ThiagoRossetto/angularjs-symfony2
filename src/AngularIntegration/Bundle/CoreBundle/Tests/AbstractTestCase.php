<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 18/06/14
 * Time: 15:07
 */
namespace AngularIntegration\Bundle\CoreBundle\Tests;

require_once(__DIR__ . "/../../../../../app/AppKernel.php");
use Doctrine\ORM\Tools\SchemaTool;


class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected $_application;

    public function getContainer()
    {
        return $this->_application->getKernel()->getContainer();
    }

    public function setUp()
    {
        $kernel = new \AppKernel("test", true);
        $kernel->boot();
        $this->_application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $this->_application->setAutoExit(false);
        $this->runConsole("doctrine:schema:drop", array("--force" => true));
        $this->runConsole("doctrine:schema:create");
        $this->runConsole("admin:create-tipo-equipamento");
        $this->runConsole("doctrine:fixtures:load", array("--append" => true));
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->_application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
}