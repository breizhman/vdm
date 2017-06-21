<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VdmRssCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vdm:rss:load')
            ->setDescription('Reads and persists the data of the RSS feed of the site VDM')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rss = $this->getContainer()->get('app.vdm.rss');
        $rss->load();
    }

}
