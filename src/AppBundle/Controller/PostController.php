<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Entity\Post;

/**
 * Controlleur de l'API REST permettant de consulter des articles en format JSON
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class PostController extends Controller
{
    /**
     * affiche une liste d'aticle au format JSON
     * 
     * @param Request $request instance de la requete HTTP
     * 
     * @Get("/api/posts")
     * @View
     */
    public function getPostsAction(Request $request)
    {
        # récupération du nom de l'auteur
        $author = null;
        if ($request->query->has('author') && !empty($request->query->get('author'))) {
            $author = $request->query->get('author');
        }

        # récupération des date de debut et de fin pour définir un interval
        $from = $to = null;
        if (
            ($request->query->has('from') && $this->validateDate($request->query->get('from')))
            &&
            ($request->query->has('to') && $this->validateDate($request->query->get('to')))
        ) {
            $from   = $request->query->get('from');
            $to     = $request->query->get('to');
        }

        $repo = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Post');

        if(!empty($author) && !empty($from) && !empty($to)) {
            # filtre sur un auteur et sur une période
            $posts = $repo->findByAuthorAndPeriod($author, $from, $to);
        } else if(!empty($author)) {
            # filtre sur un auteur
            $posts = $repo->findByAuthor($author);
        } else if(!empty($from) && !empty($to)) {
            # filtre sur une période
            $posts = $repo->findByPeriod($from, $to);
        } else {
            $posts = $repo->findAll();
        }
        
        $postsArray = [];
        foreach ($posts as $post) {
            $postsArray[] = $this->getPostToArray($post);
        }

        return ['posts' => $postsArray, 'count' => count($postsArray)];
    }

    /**
     * affiche un aticle au format JSON
     * 
     * @param integer $id identifiant de l'article a afficher
     * 
     * @Get(
     *     "/api/posts/{id}",
     *     requirements = {"id"="\d+"}
     *  )
     * @View
     */
    public function getPostAction($id)
    {
        $post = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Post')
                ->findOneByPublicId($id);

        if(!$post) {
            throw $this->createNotFoundException();
        }

        return ['post' => $this->getPostToArray($post)];
    }

    /**
     * récupère les données d'une entité Post sous forme d'un tableau
     *
     * @param      \AppBundle\Entity\Post  $post   l'entité de l'article
     *
     * @return     array                    tableau de données de l'article
     */
    public function getPostToArray(Post $post)
    {
        return [
           'id'         => $post->getPublicId(),
           'content'    => $post->getContent(),
           'date'       => $post->getDate(),
           'author'     => $post->getAuthor()->getName(),
        ];
    }

    /**
     * valide le format d'une date au format yyyy-mm-dd
     *
     * @param      string  $date   La date sous forme d'une chaine de caractère
     *
     * @return     boolean
     */
    public function validateDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}