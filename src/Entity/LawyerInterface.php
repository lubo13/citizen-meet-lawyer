<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface LawyerInterface
 * @package App\Entity
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
interface LawyerInterface
{
    public function getLawyer():?UserInterface;
}