<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<Loan>
 */
class LoanRepository extends ServiceEntityRepository
{
    private Connection $connection;
    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Loan::class);
        $this->connection = $connection;
    }

}
