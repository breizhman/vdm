<?php
/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Charge et Sauvegarde les articles du site VDM en utilisant le service AppBundle\Service\VdmRss
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class VdmRssCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('vdm:rss:load')
            ->setDescription('Reads and save the data of the RSS feed of the site VDM')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Load posts from site VDM...');

        $rss = $this->getContainer()->get('app.vdm.rss');
        $rss->load();

        $output->writeln('=> ' . $rss->countLoadedPosts() . ' posts loaded');
    }
}
