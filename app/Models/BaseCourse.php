<?php
namespace App\Models;

abstract class BaseCourse
{
    /**
     * @var string
     */
    protected $name = null;

    /**
     * Uses for callback_data with inline keyboard
     * must match the class name
     * @var string
     */
    protected $shortName = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @var string
     */
    protected $warrantyUrl = null;

    /**
     * @var string
     */
    protected $courseUrl = null;

    /**
     * @var string
     */
    protected $content = null;

    /**
     * @var int
     */
    protected $price = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getWarrantyUrl(): string
    {
        return $this->warrantyUrl;
    }

    /**
     * @return string
     */
    public function getCourseUrl(): string
    {
        return $this->courseUrl;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getPrice() : int
    {
        return $this->price;
    }

    /**
    *   There must be a parameter in the .env file
    *    called "<Class Name>_WARRANTY"
    */
    final protected function setWarrantyUrl()
    {
        $envParam = $this->getShortName() . '_WARRANTY';
        $this->warrantyUrl = getenv($envParam);
    }

    /**
     *   There must be a parameter in the .env file
     *    called "<Class Name>_COURSE"
     */
    final protected function setCourseUrl()
    {
        $envParam = $this->getShortName() . '_COURSE';
        $this->courseUrl = getenv($envParam);
    }

    abstract protected function setName();

    /**
     *   Uses for callback_data with inline keyboard
     *   must match the class name
     */
    protected function setShortName()
    {
        $classname = get_class($this);
        $pos = strrpos($classname, '\\');
        $shortName = substr($classname, $pos + 1);
        $this->shortName = $shortName;
    }

    abstract protected function setDescription();
    abstract protected function setContent();
    abstract protected function setPrice();

    public function __construct()
    {
        $this->setName();
        $this->setShortName();
        $this->setPrice();
        $this->setDescription();
        $this->setWarrantyUrl();
        $this->setCourseUrl();
        $this->setContent();
    }
}
