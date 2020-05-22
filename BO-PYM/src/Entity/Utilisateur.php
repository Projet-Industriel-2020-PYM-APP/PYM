<?php

namespace App\Entity;

use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity("email", message="L'adresse e-mail existe déjà")
 * @UniqueEntity("username")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=255, options={"default": "User"})
     * @Assert\Choice({"Admin","User"})
     */
    private $role;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenExpiresAt;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $refreshTokenExpiresAt;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isEmailVerified;

    /**
     * Get a token to use temporarily.
     *
     * This is mostly used for email confirmation or password reset.
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * You should use a token randomizer.
     * Do not forget to getRefreshTokenExpiresAt.
     *
     * Example :
     *
     * ```php
     * $user->setRefreshToken(bin2hex(random_bytes(64));
     * $expirationDate = new DateTime();
     * $expirationDate->add(new DateInterval('P1D'));
     * $user->getRefreshTokenExpiresAt($expirationDate);
     * ```
     *
     * @param string|null $refreshToken
     * @return $this
     */
    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEmailVerified(): bool
    {
        return $this->isEmailVerified;
    }

    /**
     * @param bool $isEmailVerified
     * @return Utilisateur
     */
    public function setIsEmailVerified(bool $isEmailVerified)
    {
        $this->isEmailVerified = $isEmailVerified;
        return $this;
    }

    /**
     * This is used to get access to the account.
     * This is equivalent to a email+password authentication.
     *
     * You can generate a token using the setter and your own token randomizer :
     *
     * ```php
     * $user->setToken(bin2hex(random_bytes(64));
     * ```
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * You should use a token randomizer.
     *
     * Example :
     *
     * ```php
     * $user->setToken(bin2hex(random_bytes(64));
     * ```
     * @param string|null $token
     * @return Utilisateur
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isTokenExpired(): bool
    {
        return $this->getTokenExpiresAt() <= new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getTokenExpiresAt()
    {
        return $this->tokenExpiresAt;
    }

    /**
     * @param DateTime $tokenExpiresAt
     * @return Utilisateur
     */
    public function setTokenExpiresAt(DateTime $tokenExpiresAt): self
    {
        $this->tokenExpiresAt = $tokenExpiresAt;

        return $this;
    }

    /**
     *  Generate and instanciate the "token" and the "tokenExpiresAt" properties.
     *
     *  Settings :
     *
     *  -  Token of 64 bytes (128 hexadical characters), or a token of 23 characters if entropy gathering fails
     *  -  ExpiresAt 24 hours
     */
    public function generateToken()
    {
        try {
            $this->setToken(bin2hex(random_bytes(64)));
        } catch (Exception $e) {
            $this->setToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1D'));
        $this->setTokenExpiresAt($expirationDate);
    }

    /**
     *  Generate and instanciate the "refreshToken" and the "refreshTokenExpiresAt" properties.
     *
     *  Settings :
     *
     *  -  Token of 64 bytes (128 hexadical characters), or a token of 23 characters if entropy gathering fails
     *  -  ExpiresAt 24 hours
     */
    public function generateRefreshToken()
    {
        try {
            $this->setRefreshToken(bin2hex(random_bytes(64)));
        } catch (Exception $e) {
            $this->setRefreshToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1D'));
        $this->setRefreshTokenExpiresAt($expirationDate);
    }

    public function isRefreshTokenExpired(): bool
    {
        return $this->getRefreshTokenExpiresAt() <= new DateTime();
    }

    public function getRefreshTokenExpiresAt(): ?DateTime
    {
        return $this->refreshTokenExpiresAt;
    }

    public function setRefreshTokenExpiresAt(?DateTime $refreshTokenExpiresAt): self
    {
        $this->refreshTokenExpiresAt = $refreshTokenExpiresAt;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
    }

    public function getRoles()
    {
        if ($this->getRole() == "Admin") {
            return ['ROLE_ADMIN'];
        } elseif ($this->getRole() == "User") {
            return ['ROLE_USER'];
        } else {
            return 'ROLE_USER';
        }
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
