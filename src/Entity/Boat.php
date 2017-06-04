<?php

namespace MyBoats\Entity;

/**
 * Represents a boat for the My Boats package.
 *
 * @\Doctrine\ORM\Mapping\Entity(
 * )
 * @\Doctrine\ORM\Mapping\Table(
 *     name="Boats",
 *     options={"comment": "Boats of the My Boats package"}
 * )
 */
class Boat
{
    /**
     * Create a new instance.
     *
     * @param string $name the boat name
     * @param bool $enabled is the boat enabled (published)?
     * @param int|null $length the boat length
     *
     * @return static
     */
    public static function create($name, $enabled = true, $length = null)
    {
        $result = new static();
        $result->name = (string) $name;
        $result->enabled = (bool) $enabled;
        $result->length = $length ? (int) $length : null;

        return $result;
    }

    /**
     * Initializes the instance.
     */
    protected function __construct()
    {
    }

    /**
     * The boat identifier.
     *
     * @\Doctrine\ORM\Mapping\Column(type="integer", options={"unsigned": true, "comment": "Boat identifier"})
     * @\Doctrine\ORM\Mapping\Id
     * @\Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     *
     * @var int|null
     */
    protected $id;

    /**
     * Get the boat identifier.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The boat name.
     *
     * @\Doctrine\ORM\Mapping\Column(type="string", length=255, nullable=false, options={"comment": "Boat name"})
     *
     * @var string
     */
    protected $name;

    /**
     * Get the boat name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the boat name.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setName($value)
    {
        $this->name = (string) $value;

        return $this;
    }

    /**
     * Is the boat enabled (published)?
     *
     * @\Doctrine\ORM\Mapping\Column(type="boolean", nullable=false, options={"comment": "Is the boat enabled (published)?"})
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Is the boat enabled (published)?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Is the boat enabled (published)?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setIsEnabled($value)
    {
        $this->enabled = (bool) $value;

        return $this;
    }

    /**
     * The boat length.
     *
     * @\Doctrine\ORM\Mapping\Column(type="integer", nullable=true, options={"unsigned": true, "comment": "Boat length"})
     *
     * @var int|null
     */
    protected $length;

    /**
     * Get the boat length.
     *
     * @return int|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set the boat length.
     *
     * @param int|null $value
     *
     * @return $this
     */
    public function setLength($value)
    {
        $this->length = $value ? (int) $value : null;

        return $this;
    }
}
