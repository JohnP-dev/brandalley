<?php
namespace App\Entity;
use \DateTime;
/**
 * CLass to store properties on MarchandesignActions
 */
class Actions
{
    private $index;
    private $id;
    private $label;
    private $frontLabel;
    private $position;
    private $priority;
    private $beginDate;
    private $htmlContent;

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setIndex(int $value){
        $this->index = $value;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getIndex(){
        return $this->index;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setId(string $value){
        $this->id = $value;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setLabel(string $value){
        $this->label = $value;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabel(){
        return $this->label;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setFrontLabel(string $value){
        $this->frontLabel = $value;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getFrontLabel(){
        return $this->frontLabel;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setPosition(string $value){
        $this->position = $value;
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getPosition(){
        return $this->position;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setPriority(string $value){
        $this->priority = $value;
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getPriority(){
        return $this->priority;
    }
    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setBeginDate(string $value){
        $this->beginDate = $value;
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getBeginDate(){
        $date = new DateTime($this->beginDate);
        return $date->format("d/m/Y H:i:s");
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setHtmlContent(string $value){
        $this->htmlContent = $value;
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getHtmlContent(){
        return $this->htmlContent;
    }
    
}
