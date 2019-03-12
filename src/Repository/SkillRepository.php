<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;

class SkillRepository extends EntityRepository
{

    public function getAllSkills() {
        $skills = $this->findAll();

        return $skills;
    }
}