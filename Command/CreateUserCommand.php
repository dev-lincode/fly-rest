<?php

namespace Lincode\RestApi\Bundle\Command;

use CMS\MemberBundle\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth:user:create')
            ->setDescription('Creates a new user')
            ->addArgument('name', null, InputArgument::REQUIRED, 'Specify name')
            ->addArgument('email', null, InputArgument::REQUIRED, 'Specify email')
            ->addArgument('password', null, InputArgument::REQUIRED, 'Specify password')
            ->addArgument('apikey', null, InputArgument::REQUIRED, 'Specify API key')
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info>command creates a new user.

<info>php %command.full_name% email password</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('platform.user.manager');
        $encoderService =  $encoderService = $this->getContainer()->get('security.encoder_factory');

        $user = new Member();
        $user->setName($input->getArgument('name'));
        $user->setEmail($input->getArgument('email'));

        $user->setSalt(md5(uniqid()));
        $user->setApiKey($input->getArgument('apikey'));

        $encoder = $encoderService->getEncoder($user);
        $user->setPassword($encoder->encodePassword($input->getArgument('password'), $user->getSalt()));

        $userManager->persist($user);
        $userManager->flush();
        $output->writeln(
            sprintf(
                'Added a new user with username <info>%s</info>, and API key <info>%s</info>',
                $user->getEmail(),
                $user->getApiKey()
            )
        );
    }
}
