<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\MyEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class ValidationController extends AbstractController
{
    public function __construct(private readonly ValidatorInterface $validator, private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/validate', name: 'app_validation')]
    public function validate(): JsonResponse
    {
        $createdEntity = new MyEntity();
        $createdEntity->setFirstname('Michael');
        $createdEntity->setLastname('Hirschler');

        // this is intended
        $errors = $this->validator->validate($createdEntity);
        Assert::count($errors, 1);
        Assert::same($errors[0]->getPropertyPath(), 'lastname');

        $this->em->persist($createdEntity);
        $this->em->flush();
        $this->em->detach($createdEntity);

        $entity = $this->em->getReference(MyEntity::class, 1);
        $errors = $this->validator->validate($entity);
        Assert::count($errors, 1);
        Assert::same($errors[0]->getPropertyPath(), 'lastname');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ValidationController.php',
        ]);
    }
}
