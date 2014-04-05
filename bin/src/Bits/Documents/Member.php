<?php
/**
 * Member
 *
 * @copyright mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

namespace Member\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(collection="Members")
 */
class Member
{
    /**
     * Member Id
     * @var string
     * @ODM\Id
     */

    protected $id;
    /**
     * Name
     * @var Array
     * @ODM\Hash
     */
    protected $name;


    /**
     * Password
     * @var string
     * @ODM\String
     */
    protected $password;

    /**
     * Email
     * @var string
     * @ODM\String
     */
    protected $email;



    /**
     * Created On
     * @var Date
     * @ODM\Date
     */
    protected $createdOn;


    /**
     * Set Properties
     * @param Array $properties
     * @return Object member
     */
    public function setProperties(Array $properties)
    {
        foreach($properties as $property => $value){
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
        return $this;
    }


    /**
     * Getter
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * Setter
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
}