<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface CitizenInterface
 * @package App\Entity
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
interface CitizenInterface
{
    public function getCitizen():?UserInterface;
}