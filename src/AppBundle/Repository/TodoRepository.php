<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Todo;
use AppBundle\Enum\TodoStatus;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

/**
 * TodoRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TodoRepository extends EntityRepository
{
    public function findAllWithDueDateInNext24h()
    {
        $dateNow = Carbon::now();
        $dateFuture = Carbon::now()->addHours(24);

        // t.id, t.content, t.dueDate, u.email, u.username
        $dql = 'SELECT t, u
                FROM AppBundle:Todo t
                JOIN t.user u
                WHERE t.dueDate > ?1
                AND t.dueDate < ?2
                AND t.status = ?3';

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $dateNow)
            ->setParameter(2, $dateFuture)
            ->setParameter(3, TodoStatus::PENDING)
            ->getResult(); // getArrayResult()
    }
}
