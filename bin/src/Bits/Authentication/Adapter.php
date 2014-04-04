<?php
/**
 * Bits
 *
 * @copyright Mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

namespace Bits\Authentication;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Authentication Adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * User email address
     * @var String
     */
    private $email;

    /**
     * User password
     * @var String
     */
    private $password;


    /**
     * Member Query Object from Doctrine
     * @var Object
     */
    protected $memberDocumentRepository;


    public function __construct(DocumentRepository $memberDocumentRepository, $email, $password)
    {
        $this->memberDocumentRepository = $memberDocumentRepository;
        $this->email = $email;
        $this->password = $password;

    }

    public function authenticate()
    {
        $member = $this->memberDocumentRepository->findOneByEmail($this->email);

        if (empty($member)) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, [], ['No member exists with the credentials provided']);
        }

        if (!password_verify($this->password, $member->password)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, [], ['Wrong Credentials ']);
        }

        return new Result(Result::SUCCESS, $member->id, []);

    }

}