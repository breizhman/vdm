<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use FeedIo\FeedIo;
use AppBundle\Entity\Post;
use AppBundle\Entity\Author;

class VdmRss
{
    private $rssUrl;
    private $feedio;
    private $entityManager;
    private $logger;
    private $limitPosts;

    private $posts;

    public function __construct(FeedIo $feedio, EntityManager  $entityManager, LoggerInterface $logger, $rssUrl, $limitPosts)
    {   
        $this->posts = array();
        $this->limitPosts    = $limitPosts;
        $this->rssUrl    = $rssUrl;
        $this->feedio    = $feedio;
        $this->entityManager    = $entityManager;
        $this->logger =  $logger;
    }

    public function read($page = null)
    {
        $url = $this->rssUrl;
        if (is_integer($page)) {
            $url.= '?page='.intval($page);
        }
        return $this->feedio->read($url)->getFeed();
    }

    public function load() {

        $total = 0;
        $index_page = 1;

        do {
            $total+= $this->loadPostsByPage($index_page);
            $index_page++;

        } while($total < $this->limitPosts);

        $this->posts = array_slice($this->posts,0,$this->limitPosts);

        $this->save();
    }

    public function loadPostsByPage($page = null)
    {
        $cpt = 0;
        $feed = $this->read($page);

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

    public function save()
    {
        $authorEntites = array();
        foreach($this->posts as $post)
        {
            if(isset($authorEntites[$post['author']])) {
                $authorEntity = $authorEntites[$post['author']];
            } else {
                $authorEntity = $this->entityManager->getRepository('AppBundle:Author')->findOneByName($post['author']);

                if(!$authorEntity) {
                    $authorEntity = new Author();
                    $authorEntity->setName($post['author']);
                }

                $authorEntites[$post['author']] = $authorEntity;
            }

            $postEntity = $this->entityManager->getRepository('AppBundle:Post')->findOneByPublicId($post['publicId']);

            if(!$postEntity) {
                $postEntity = new Post();
                $postEntity->setPublicId($post['publicId']);
            }

            $postEntity->setContent($post['content']);
            $postEntity->setDate($post['date']);
            $postEntity->setAuthor($authorEntity);

            $this->entityManager->persist($postEntity);
        }

        $this->entityManager->flush();
    }
}