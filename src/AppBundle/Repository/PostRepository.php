<?php
/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Repository de l'entité Post
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Récupère tous les articles, ordonné par date de parution
     *
     * @return array liste des articles
     */
    public function findAll()
    {
        $qb = $this->createQueryBuilder('p');

        $this->orderByDate($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère tous les articles d'un auteur
     *
     * @param string $author Nom de l'auteur
     *
     * @return array liste des articles
     */
    public function findByAuthor($author)
    {
        $qb = $this->createQueryBuilder('p');

        $this->joinAuthorByName($qb, $author);
        $this->orderByDate($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère la liste des articles paru dans une prériode
     *
     * @param string $from la date de début de la prériode
     * @param string $to   la date de fin de la prériode
     *
     * @return array liste des articles
     */
    public function findByPeriod($from, $to)
    {
        $qb = $this->createQueryBuilder('p');

        $this->whereDatePeriod($qb, $from, $to);
        $this->orderByDate($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère la liste des articles d'un auteur paru dans une prériode
     *
     * @param string $author Nom de l'auteur
     * @param string $from   la date de début de la prériode
     * @param string $to     la date de fin de la prériode
     *
     * @return array liste des articles
     */
    public function findByAuthorAndPeriod($author, $from, $to)
    {
        $qb = $this->createQueryBuilder('p');

        $this->joinAuthorByName($qb, $author);
        $this->whereDatePeriod($qb, $from, $to);
        $this->orderByDate($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * ajoute une jointure pour filtrer sur un auteur
     *
     * @param \Doctrine\ORM\QueryBuilder $qb   instance du query builder
     * @param string                     $name Nom de l'auteur
     */
    public function joinAuthorByName(QueryBuilder $qb, $name)
    {
        $qb->join('p.author', 'a', 'WITH', 'a.name = :name')
            ->setParameter('name', $name);
    }

    /**
     * ajoute une condition pour filtrer les articles sur une période
     *
     * @param \Doctrine\ORM\QueryBuilder $qb   instance du query builder
     * @param string                     $from la date de début de la prériode
     * @param string                     $to   la date de fin de la prériode
     */
    public function whereDatePeriod(QueryBuilder $qb, $from, $to)
    {

        $from = new \Datetime($from);
        // on ne tiens pas compte des heures
        $from->format('Y-m-d');

        $to = new \Datetime($to);
        // on ne tiens pas compte des heures
        $to->format('Y-m-d');
        // astuce pour prendre en compte la jourrnée complète
        $to->modify('+1 day -1 minutes');

        $qb
          ->andWhere('p.date BETWEEN :start AND :end')
              ->setParameter('start', $from)
              ->setParameter('end', $to)
        ;
    }

    /**
     * ajoute un trie par date de parution
     *
     * @param \Doctrine\ORM\QueryBuilder $qb instance du query builder
     */
    public function orderByDate(QueryBuilder $qb)
    {
        $qb->orderBy('p.date', 'DESC');
    }
}
