<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\Club;
use Doctrine\ORM\EntityManagerInterface;


class RegisterController extends AbstractController
{

    private UserPasswordHasherInterface $passwordHasher;
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }


    #[Route('/register', name: 'app_register')]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $plainPassword = $data['password'];

        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);
        $user->setMail($data['mail']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setSeanceLeft(0);
        $user->setTelNumber($data['tel_number']);
        $user->setRoles($data['roles']);

        foreach($data['club'] as $club) {
            $segments = explode('/', $club);
            $clubid = end($segments);
            $user->addClub($this->entityManager->getRepository(Club::class)->find($clubid));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}
