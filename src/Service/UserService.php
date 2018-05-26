<?php

namespace App\Service;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return null|object
     */
    public function getUserById(int $id)
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->find($id);
    }

    /**
     * @return array
     */
    public function getAllUsers()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findAll();
    }
}