<?php

namespace Lincode\RestApi\Bundle\Command;

use OAuth2;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CredentialsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth:credentials')
            ->setDescription('Executes OAuth2 Credentials grant');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $credentialsClient = $this->getContainer()->get('oauth_client.credentials_client');
        $accessToken = $credentialsClient->getAccessToken();
        $output->writeln(sprintf('Obtained Access Token: <info>%s</info>', $accessToken));

        $url = $this->getContainer()->getParameter('restapi_url');
        $output->writeln(sprintf('Requesting: <info>%s</info>', $url));
        $response = $credentialsClient->fetch($url);
        $output->writeln(sprintf('Response: <info>%s</info>', var_export($response, true)));
    }
}
