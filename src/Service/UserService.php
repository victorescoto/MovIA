<?php

namespace App\Service;

use App\Entity\User;
use App\Http\Request;
use App\Repository\UserRepository;

class UserService
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUsers()
    {
        return $this->repository->findAll();
    }

    public function createUser(Request $request): User
    {
        $user = (new User)
            ->setEmail($request->json->get('email'))
            ->setPassword($request->json->get('password'));

        return $this->repository->save($user);
    }

    public function updateUser(Request $request, User $user): User
    {
        $user->setEmail($request->json->get('email'));

        return $this->repository->save($user);
    }

    public function deleteUser(User $user): void
    {
        $this->repository->delete($user);
    }
}
