<?php

namespace App\DTO\V1;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;
class UserRegistrationDto
{
    #[Assert\NotBlank(message: 'Email address is required')]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email address.',
        mode: 'strict'
    )]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Email address cannot be longer than {{ limit }} characters'
    )]
    #[AppAssert\UniqueEmail]
    public string $email;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(
        min: 8,
        max: 4096,
        minMessage: 'Password must be at least {{ limit }} characters long',
        maxMessage: 'Password cannot be longer than {{ limit }} characters'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        message: 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
    )]
    public string $password;

    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Length(min: 2, max: 50)]
    public string $firstName;

    #[Assert\NotBlank(message: "Last name is required.")]
    #[Assert\Length(min: 2, max: 50)]
    public string $lastName;
}
