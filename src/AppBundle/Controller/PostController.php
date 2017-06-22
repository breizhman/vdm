<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

use AppBundle\Entity\Post;

class PostController extends Controller
{
    /**
     * @Get("/api/posts")
     * @View
     */
    public function getPostsAction(Request $request)
    {
        $repo = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Post');

        $author = null;
        if ($request->query->has('author') && !empty($request->query->get('author'))) {
            $author = $request->query->get('author');
        }

        $from = $to = null;
        if (
            ($request->query->has('from') && $this->validateDate($request->query->get('from')))
            &&
            ($request->query->has('to') && $this->validateDate($request->query->get('to')))
        ) {
            $from   = $request->query->get('from');
            $to     = $request->query->get('to');
        }

        if(!empty($author) && !empty($from) && !empty($to)) {
            $posts = $repo->findByAuthorAndPeriod($author, $from, $to);
        } else if(!empty($author)) {
            $posts = $repo->findByAuthor($author);
        } else if(!empty($from) && !empty($to)) {
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
        
        $postsArray = [];
        if ($post) {
            $postsArray = $this->getPostToArray($post);
        }

        return ['post' => $postsArray];
    }

    public function getPostToArray(Post $post)
    {
        return [
           'id'         => $post->getPublicId(),
           'content'    => $post->getContent(),
           'date'       => $post->getDate(),
           'author'     => $post->getAuthor()->getName(),
        ];
    }

    public function validateDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}