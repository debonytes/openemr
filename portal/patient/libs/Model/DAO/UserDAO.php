<?php
/**
 * UserDAO.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2016-2017 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("UserMap.php");

/**
 * UserDAO provides object-oriented access to the users table.  This
 * class is automatically generated by ClassBuilder.
 *
 * WARNING: THIS IS AN AUTO-GENERATED FILE
 *
 * This file should generally not be edited by hand except in special circumstances.
 * Add any custom business logic to the Model class which is extended from this DAO class.
 * Leaving this file alone will allow easy re-generation of all DAOs in the event of schema changes
 *
 * @package Openemr::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class UserDAO extends Phreezable
{
    /** @var int */
    public $Id;

    /** @var string */
    public $Username;

    /** @var longtext */
    public $Password;

    /** @var int */
    public $Authorized;

    /** @var longtext */
    public $Info;

    /** @var int */
    public $Source;

    /** @var string */
    public $Fname;

    /** @var string */
    public $Mname;

    /** @var string */
    public $Lname;

    /** @var string */
    public $Federaltaxid;

    /** @var string */
    public $Federaldrugid;

    /** @var string */
    public $Upin;

    /** @var string */
    public $Facility;

    /** @var int */
    public $FacilityId;

    /** @var int */
    public $SeeAuth;

    /** @var int */
    public $Active;

    /** @var string */
    public $Npi;

    /** @var string */
    public $Title;

    /** @var string */
    public $Specialty;

    /** @var string */
    public $Billname;

    /** @var string */
    public $Email;

    /** @var string */
    public $EmailDirect;

    /** @var string */
    public $EserUrl;

    /** @var string */
    public $Assistant;

    /** @var string */
    public $Organization;

    /** @var string */
    public $Valedictory;

    /** @var string */
    public $Street;

    /** @var string */
    public $Streetb;

    /** @var string */
    public $City;

    /** @var string */
    public $State;

    /** @var string */
    public $Zip;

    /** @var string */
    public $Street2;

    /** @var string */
    public $Streetb2;

    /** @var string */
    public $City2;

    /** @var string */
    public $State2;

    /** @var string */
    public $Zip2;

    /** @var string */
    public $Phone;

    /** @var string */
    public $Fax;

    /** @var string */
    public $Phonew1;

    /** @var string */
    public $Phonew2;

    /** @var string */
    public $Phonecell;

    /** @var string */
    public $Notes;

    /** @var int */
    public $CalUi;

    /** @var string */
    public $Taxonomy;

    /** @var string */
    public $SsiRelayhealth;

    /** @var int */
    public $Calendar;

    /** @var string */
    public $AbookType;

    /** @var date */
    public $PwdExpirationDate;

    /** @var longtext */
    public $PwdHistory1;

    /** @var longtext */
    public $PwdHistory2;

    /** @var string */
    public $DefaultWarehouse;

    /** @var string */
    public $Irnpool;

    /** @var string */
    public $StateLicenseNumber;

    /** @var string */
    public $NewcropUserRole;

    /** @var int */
    public $Cpoe;

    /** @var string */
    public $PhysicianType;

    /** @var int */
    public $PortalUser;

    /**
     * Returns a dataset of FormHearing objects with matching ExaminerId
     * @param Criteria
     * @return DataSet
     */
    public function GetExaminerFormHearings($criteria = null)
    {
        return $this->_phreezer->GetOneToMany($this, "examinerlkup", $criteria);
    }

    /**
     * Returns a dataset of FormHearing objects with matching ReviewerId
     * @param Criteria
     * @return DataSet
     */
    public function GetReviewerFormHearings($criteria = null)
    {
        return $this->_phreezer->GetOneToMany($this, "reviewerlkup", $criteria);
    }
}
