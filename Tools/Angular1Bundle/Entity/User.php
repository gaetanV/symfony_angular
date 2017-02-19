<?php
namespace Tools\Angular1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 * @Groups ( {
 *      registration= "ROLE_USER"
 * })
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(
     *   groups={"registration"}
     * )
     * @Assert\Length(
     *      groups={"user"},
     *      min = 4,
     *      max = 50,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="username", type="string", length=30)
     */
    private $username;

    /**
     * @var string
     * @Assert\Regex(
     *       pattern="/[0-9]+/",
     *       match=  true,
     *       groups= {"registration"}
     * )
     * @Assert\Regex(
     *       pattern="/[a-zA-Z]+/",
     *       match=  true,
     *       groups= {"registration"}
     * )
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;
    
      /**
     * @var string
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @ORM\Column(name="email", type="string", length=30)
     */
    private $email;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password,PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
     /**
     * {@inheritdoc}
     */
    public function getSalt(){
        return null;
    }
    
     /**
     * {@inheritdoc}
     */
    public function getRoles(){
        return array("ROLE_USER");
    }
    
    /**
    * {@inheritdoc}
    */
    public function eraseCredentials(){ }
}
