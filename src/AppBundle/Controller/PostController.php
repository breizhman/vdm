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

        if ($request->query->has('author') && ($request->query->has('from') && $request->query->has('to'))) {

            $posts = $repo->findByAuthorAndPeriod(
                $request->query->get('author'), 
                $request->query->get('from'), 
                $request->query->get('to')
            );

        } else {
            if ($request->query->has('author')) {
                $posts = $repo->findByAuthor($request->query->get('author'));
            } else if ($request->query->has('from') && $request->query->has('to')) {
                $posts = $repo->findByPeriod(
                    $request->query->get('from'), 
                    $request->query->get('to')
                );
            } else {
                $posts = $repo->findAll();
            }
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
}