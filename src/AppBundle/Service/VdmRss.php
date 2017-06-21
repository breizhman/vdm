<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use FeedIo\FeedIo;
use AppBundle\Entity\Post;

class VdmRss
{
    private $rssUrl;
    private $feedio;
    private $entityManager;
    private $logger;

    public function __construct($rssUrl, FeedIo $feedio, EntityManager  $entityManager, LoggerInterface $logger)
    {
        $this->rssUrl    = $rssUrl;
        $this->feedio    = $feedio;
        $this->entityManager    = $entityManager;
        $this->logger =  $logger;
    }

    public function load()
    {
        $feed = $this->feedio->read($this->rssUrl)->getFeed();

        foreach ( $feed as $item ) {

            $post = $this->entityManager->getRepository('AppBundle:Post')->findOneByPublicId($item->getPublicId());

            if(!$post) {
                $post = new Post();
                $post->setPublicId($item->getPublicId());
            }

            $post->setContent(strip_tags($item->getDescription()));
            $post->setDate($item->getLastModified());
            $post->setAuthor($item->getAuthor()->getName());

            $this->entityManager->persist($post);
        }

        $this->entityManager->flush();
    }
}