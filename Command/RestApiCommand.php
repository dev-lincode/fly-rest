<?php
namespace Lincode\RestApi\Bundle\Command;
use Voryx\RESTGeneratorBundle\Command\GenerateDoctrineRESTCommand;
use Voryx\RESTGeneratorBundle\Generator\DoctrineRESTGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class RestApiCommand extends GenerateDoctrineRESTCommand {
    protected $generator;
    protected function configure()
    {
        parent::configure();
        $this->setName('fly:generate:rest');
        $this->setDescription('Generate RESTAPI Bundle!');
    }
    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = $this->createGenerator();
            $this->generator->setSkeletonDirs( __DIR__.'/../Resources/views/Generator/' );
        }
        return $this->generator;
    }
    protected function createGenerator($bundle = NULL)
    {
        return new DoctrineRESTGenerator($this->getContainer()->get('filesystem'));
    }
}