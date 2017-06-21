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
    private $default_nb_post;

    private $posts;

    public function __construct(FeedIo $feedio, EntityManager  $entityManager, LoggerInterface $logger, $rssUrl, $default_nb_post)
    {   
        $this->posts = array();
        $this->default_nb_post    = $default_nb_post;
        $this->rssUrl    = $rssUrl;
        $this->feedio    = $feedio;
        $this->entityManager    = $entityManager;
        $this->logger =  $logger;
    }

    public function load($saveToBdd = true)
    {
        $this->loadPosts();

        if($saveToBdd) {
            $this->savePosts();
        }
    }

    private function loadPosts($nbPost = null) {

        if(is_null($nbPost)) {
            $nbPost = $this->default_nb_post;
        }

        $total = 0;
        $index_page = 1;

        $feed = $this->feedio->read($this->rssUrl)->getFeed();

        do {

            $total+= $this->loadPostsByPage($index_page);
            $index_page++;

        } while($total < $nbPost);

        $this->posts = array_slice($this->posts,0,$nbPost);
    }

    private function loadPostsByPage($page = null)
    {
        $cpt = 0;
        $url = $this->rssUrl;
        if(is_integer($page)) {
            $url.= '?page='.intval($page);
        }

        $feed = $this->feedio->read($url)->getFeed();

        foreach ( $feed as $item ) {

            $this->posts[] = array(
                'publicId'  => $item->getPublicId(),
                'content'   => strip_tags($item->getDescription()),
                'date'      => $item->getLastModified(),
                'author'    => $item->getAuthor()->getName()
            );

            $cpt++;
        }

        return $cpt;
    }

    private function savePosts()
    {
        foreach($this->posts as $post)
        {
            $entity_post = $this->entityManager->getRepository('AppBundle:Post')->findOneByPublicId($post['publicId']);

            if(!$entity_post) {
                $entity_post = new Post();
                $entity_post->setPublicId($post['publicId']);
            }

            $entity_post->setContent($post['content']);
            $entity_post->setDate($post['date']);
            $entity_post->setAuthor($post['author']);

            $this->entityManager->persist($entity_post);
        }

        $this->entityManager->flush();
    }
}