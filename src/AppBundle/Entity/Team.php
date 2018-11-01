<?php

namespace AppBundle\Entity;


class Team
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;

    /**
     * Team constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        if (strlen($name) === 0) {
            throw new \InvalidArgumentException('Name cannot be empty string!');
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

}