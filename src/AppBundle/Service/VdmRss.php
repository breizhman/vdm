<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use FeedIo\FeedIo;
use AppBundle\Entity\Post;
use AppBundle\Entity\Author;

/**
 * Service permettant de récupérer les données du site VDM via leur flux RSS
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class VdmRss
{
    /**
     * url du flus RSS
     * @var string
     */
    private $rssUrl;

    /**
     * instance du service permettant de traiter un flux RSS
     * @var FeedIo\FeedIo
     */
    private $feedio;

    /**
     * instance de l'entitity manager de Doctrine
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * instance du logger
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Nombre limite d'articles à charger
     * @var integer
     */
    private $limitPosts;

    /**
     * liste des articles chargés
     * @var array
     */
    private $posts;

    /**
     * création de l'instance du service
     *
     * @param      \FeedIo\FeedIo               $feedio         instance du service permettant de traiter un flux RSS
     * @param      \Doctrine\ORM\EntityManager  $entityManager  instance de l'entitity manager de Doctrine
     * @param      \Psr\Log\LoggerInterface     $logger         instance du logger
     * @param      string                       $rssUrl         url du flus RSS
     * @param      integer                      $limitPosts     Nombre limite d'articles à charger
     */
    public function __construct(FeedIo $feedio, EntityManager  $entityManager, LoggerInterface $logger, $rssUrl, $limitPosts)
    {
        $this->posts            = [];
        $this->limitPosts       = $limitPosts;
        $this->rssUrl           = $rssUrl;
        $this->feedio           = $feedio;
        $this->entityManager    = $entityManager;
        $this->logger           = $logger;
    }

    /**
     * Lit et récupère la liste des articles du flux RSS
     *
     * @param      integer  $numPage   Numéro de la page du flux
     *
     * @return     array            Liste des articles
     */
    public function read($numPage = null)
    {
        $url = $this->rssUrl;
        if (is_integer($numPage)) {
            $url.= '?page='.intval($numPage);
        }

        $data = [];
        try {
            $data = $this->feedio->read($url)->getFeed();
        } catch (\Exception $e) {
            $this->logger->error('['.__CLASS__.':'.__FUNCTION__.'] Erreur lors de la récupération des articles du site', array('url' => $url, 'exception' => $e->getMessage()));
        }

        return $data;
    }

    /**
     * Charge et sauvegarde les articles du flux RSS
     */
    public function load() {

        $this->logger->debug('['.__CLASS__.':'.__FUNCTION__.'] Chargement des articles du site VDM', array('url' => $this->rssUrl));

        # nombre total d'articles déjà chargé
        $total = 0;
        # numéro de la page courante
        $numPage = 1;

        # on parcourt toutes les pages du flux RSS tant qu'on est pas arrivé à la limite
        do {
            $total+= $this->loadPostsByPage($numPage);
            $numPage++;

        } while($total < $this->limitPosts);

        # dans le cas où trop d'articles ont été récupéré, on scinde le tableau
        $this->posts = array_slice($this->posts,0,$this->limitPosts);

        $this->save();

        $this->logger->debug('['.__CLASS__.':'.__FUNCTION__.'] Fin du chargement', array('countPosts' => $this->countLoadedPosts()));
    }

    /**
     * chargement des articles par page
     *
     * @param      integer   $numPage   Numéro de la page
     *
     * @return     integer  Nombre d'articles chargés
     */
    public function loadPostsByPage($numPage = null)
    {
        $cpt = 0;
        $feed = $this->read($numPage);

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

    /**
     * Sauvegarde des articles en base de données
     */
    public function save()
    {
        $this->logger->debug('['.__CLASS__.':'.__FUNCTION__.'] Sauvegarde des articles');

        # liste des entités des auteus, utilisé pour éviter de dupliquer un auteur
        $authorEntites = [];

        foreach ($this->posts as $post) {

            # si l'auteur à déjà été créé lors d'une précdente ittération, on le récupère
            if (isset($authorEntites[$post['author']])) {
                $authorEntity = $authorEntites[$post['author']];
            } else {
                # sinon on récupère l'auteur en BDD, s'il n'existe pas, on le créé
                $authorEntity = $this->entityManager->getRepository('AppBundle:Author')->findOneByName($post['author']);

                if(!$authorEntity) {
                    $authorEntity = new Author();
                    $authorEntity->setName($post['author']);
                }

                $authorEntites[$post['author']] = $authorEntity;
            }

            # on récupère l'article en BDD, s'il n'existe pas, on le créé
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

    /**
     * Compte le nombre d'articles chargé
     *
     * @return     string  Number of loaded posts.
     */
    public function countLoadedPosts()
    {
        return count($this->posts);
    }
}