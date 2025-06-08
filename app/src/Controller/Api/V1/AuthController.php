<?php

namespace App\Controller\Api\V1;

use App\DTO\V1\UserRegistrationDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller that handles user registration and login for the V1 API.
 */
#[Route('/api/v1/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface         $entityManager,
        private readonly UserPasswordHasherInterface    $passwordHasher,
        private readonly ValidatorInterface             $validator,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * Handle user registration.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \JsonException
     */
    #[Route('/register', name: 'api_v1_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            // Deserialize JSON into DTO
            $registrationDto = $this->serializer->deserialize(
                $request->getContent(),
                UserRegistrationDto::class,
                'json'
            );
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid JSON payload',
                'errors' => ['Invalid request format']
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate DTO
        $errors = $this->validator->validate($registrationDto);

        if (count($errors) > 0) {
            return $this->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->formatValidationErrors($errors)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }



        // Create a user object and set properties
        $user = new User();
        $user
            ->setEmail($registrationDto->email)
            ->setPassword($registrationDto->password)
            ->setRoles(['ROLE_USER'])
            ->setFirstName($registrationDto->firstName)
            ->setLastName($registrationDto->lastName)
        ;

        // Hash the password and persist the user
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword())
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Respond with HTTP 201 Created
        return $this->json([
            'status' => 'success',
            'message' => 'User registered successfully.'
        ],
            Response::HTTP_CREATED
        );
    }

    /**
     * This endpoint uses `lexik/jwt-authentication-bundle` for login.
     * The bundle automatically handles this functionality through its `/login` route.
     */
    #[Route('/login', name: 'api_v1_auth_login', methods: ['POST'])]
    public function login(): void
    {
        die('login');
    }

    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[$error->getPropertyPath()][] = $error->getMessage();
        }
        return $formattedErrors;
    }
}
