<?php

declare(strict_types=1);

namespace tests\UseCase\OrderedWithGetterAndSetterFirst\Regression;

use PedroTroller\CS\Fixer\ClassNotation\OrderedWithGetterAndSetterFirstFixer;
use tests\UseCase;

class Case1 implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new OrderedWithGetterAndSetterFirstFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return <<<'PHP'
<?php

namespace Domain\Model;

use DateTimeImmutable;
use Domain\Uuid;
use LogicException;
use Webmozart\Assert\Assert;

class Attendee
{
    const TYPE_ASSOCIATION = 'association';
    const TYPE_JOURNALIST  = 'journalist';
    const TYPE_OWNER       = 'owner';
    const TYPE_STAFF       = 'staff';
    const TYPE_OTHER_GUEST = 'other_guest';
    const TYPE_RETAILER    = 'retailer';
    const TYPE_SPONSOR     = 'sponsor';

    const STATUS_INCOMPLETE   = 'incomplete';
    const STATUS_PENDING      = 'pending';
    const STATUS_ACCEPTED     = 'accepted';
    const STATUS_DECLINED     = 'declined';
    const STATUS_WAITING_LIST = 'waiting list';
    const STATUS_CONFIRMED    = 'confirmed';
    const STATUS_CANCELLED    = 'cancelled';

    const PASS_MEMBER   = 'member';
    const PASS_ATTENDEE = 'attendee';
    const PASS_1_TO_1   = '1_to_1';

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $givenName;

    /**
     * @var string
     */
    private $familyName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var null|string
     */
    private $password;

    /**
     * @var Telephone
     */
    private $telephone;

    /**
     * @var string
     */
    private $status;

    /**
     * @var DateTimeImmutable
     */
    private $registeredAt;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string[]
     */
    private $countriesInCharge;

    /**
     * @var string[]
     */
    private $areasOfExpertise;

    /**
     * @var LinkedIn\Profile
     */
    private $linkedIn;

    /**
     * @var null|string
     */
    private $passType;

    /**
     * @var null|string
     */
    private $jobTitle;

    /**
     * @var null|string
     */
    private $industry;

    /**
     * @var null|string
     */
    private $seniority;

    /**
     * @var null|string
     */
    private $challenge;

    /**
     * @var Organisation
     */
    private $workForOrganisation;

    /**
     * @var Sponsor
     */
    private $workForSponsor;

    /**
     * @var Invitation
     */
    private $invitation;

    /**
     * @var null|Assistant
     */
    private $assistant;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $emailVerified;

    /**
     * @var Invitation[]
     */
    private $sentInvitations;

    /**
     * @var string | null
     */
    private $picture;

    /**
     * @var null|TripDetails
     */
    private $tripDetails;

    /**
     * @var array
     */
    private $statusHistory = [];

    private function __construct(
        string $type,
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $picture = null,
        string $country = null,
        array  $countriesInCharge = null,
        $workFor = null,
        array  $areasOfExpertise = null,
        string $status = self::STATUS_ACCEPTED,
        ?string $passType = self::PASS_ATTENDEE,
        string $jobTitle = null,
        string $industry = null,
        string $seniority = null,
        string $challenge = null,
        Assistant $assistant = null,
        TripDetails $tripDetails = null
    ) {
        $this->type  = $type;
        $this->email = $email;

        $this->givenName         = $givenName;
        $this->familyName        = $familyName;
        $this->password          = null;
        $this->telephone         = $telephone;
        $this->picture           = $picture;
        $this->country           = $country;
        $this->countriesInCharge = $countriesInCharge ?: [];
        $this->areasOfExpertise  = $areasOfExpertise ?: [];
        $this->status            = $status;
        $this->passType          = $passType;
        $this->jobTitle          = $jobTitle;
        $this->industry          = $industry;
        $this->seniority         = $seniority;
        $this->challenge         = $challenge;
        $this->assistant         = $assistant;
        $this->tripDetails       = $tripDetails;

        $this->identifier    = Uuid::generate();
        $this->password      = null;
        $this->registeredAt  = new DateTimeImmutable();
        $this->linkedIn      = new LinkedIn\Profile();
        $this->emailVerified = false;

        $this->pushStatusHistory($status);

        Assert::oneOf($type, [
            self::TYPE_ASSOCIATION,
            self::TYPE_JOURNALIST,
            self::TYPE_OWNER,
            self::TYPE_STAFF,
            self::TYPE_OTHER_GUEST,
            self::TYPE_RETAILER,
            self::TYPE_SPONSOR,
        ]);

        Assert::oneOf($passType, [
            self::PASS_MEMBER,
            self::PASS_ATTENDEE,
            self::PASS_1_TO_1,
        ]);

        if (null === $workFor) {
            return;
        }

        if ($workFor instanceof Organisation) {
            $this->workForOrganisation = $workFor;

            return;
        }

        if ($workFor instanceof Sponsor) {
            $this->workForSponsor = $workFor;

            return;
        }

        throw new LogicException('$workFor have to be an Organisation or a Sponsor');
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName)
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName)
    {
        $this->familyName = $familyName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        if ($email !== $this->email) {
            $this->emailVerified = false;
        }

        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getTelephone(): ?Telephone
    {
        if (null === $this->telephone) {
            return null;
        }

        return empty($this->telephone->getRegion()) || empty($this->telephone->getNationalNumber())
            ? null
            : $this->telephone;
    }

    public function setTelephone(Telephone $telephone)
    {
        $this->telephone = $telephone;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        $this->pushStatusHistory($status);
    }

    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public static function createASponsor(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        array $countriesInCharge = null,
        Sponsor $workFor = null,
        string $jobTitle = null,
        string $picture = null,
        array  $areasOfExpertise = [],
        string $passType = self::PASS_MEMBER,
        Assistant $assistant = null
    ) {
        return new self(
            self::TYPE_SPONSOR,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            $countriesInCharge,
            $workFor,
            $areasOfExpertise,
            self::STATUS_ACCEPTED,
            $passType,
            $jobTitle,
            null,
            null,
            null,
            $assistant
        );
    }

    public static function createARetailer(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $picture = null,
        string $country = null,
        array $countriesInCharge = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $industry = null,
        string $seniority = null,
        string $challenge = null,
        array $areasOfExpertise = null,
        Assistant $assistant = null
    ) {
        return new self(
            self::TYPE_RETAILER,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            $countriesInCharge,
            $workFor,
            $areasOfExpertise,
            self::STATUS_INCOMPLETE,
            self::PASS_ATTENDEE,
            $jobTitle,
            $industry,
            $seniority,
            $challenge,
            $assistant
        );
    }

    public static function createAStaff(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_STAFF,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_CONFIRMED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public static function createAnAssociation(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_ASSOCIATION,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_ACCEPTED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public static function createAJournalist(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_JOURNALIST,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_ACCEPTED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public static function createAnOtherGuest(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_OTHER_GUEST,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_ACCEPTED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public function isStaff(): bool
    {
        return in_array($this->type, [
            self::TYPE_OWNER,
            self::TYPE_STAFF,
        ]);
    }

    public function isOwner(): bool
    {
        return self::TYPE_OWNER === $this->type;
    }

    public function isEqualTo(self $other): bool
    {
        return $other->getIdentifier() === $this->getIdentifier();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    public function getCountriesInCharge(): array
    {
        return $this->countriesInCharge;
    }

    public function setCountriesInCharge(array $countriesInCharge)
    {
        $this->countriesInCharge = $countriesInCharge;
    }

    public function getAreasOfExpertise(): array
    {
        return $this->areasOfExpertise;
    }

    public function setAreasOfExpertise(array $areasOfExpertise)
    {
        $this->areasOfExpertise = $areasOfExpertise;
    }

    public function getPassType(): ?string
    {
        return $this->passType;
    }

    public function setPassType(string $passType = self::PASS_MEMBER)
    {
        Assert::oneOf($passType, [
            self::PASS_MEMBER,
            self::PASS_ATTENDEE,
            self::PASS_1_TO_1,
        ]);

        $this->passType = $passType;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    public function getIndustry(): ?string
    {
        return $this->industry;
    }

    public function setIndustry(string $industry)
    {
        $this->industry = $industry;
    }

    public function getSeniority(): ?string
    {
        return $this->seniority;
    }

    public function setSeniority(string $seniority)
    {
        $this->seniority = $seniority;
    }

    public function getChallenge(): ?string
    {
        return $this->challenge;
    }

    public function setChallenge(string $challenge)
    {
        $this->challenge = $challenge;
    }

    /**
     * @return null|Organisation|Sponsor
     */
    public function getWorkFor()
    {
        if (null !== $this->workForSponsor) {
            return $this->workForSponsor;
        }

        if (null !== $this->workForOrganisation && !$this->workForOrganisation->empty()) {
            return $this->workForOrganisation;
        }
    }

    public function setWorkFor(Organisation $workFor)
    {
        $this->workForOrganisation = $workFor;
    }

    public function attachToInvitation(Invitation $invitation)
    {
        if (null !== $this->invitation) {
            throw new LogicException('There is already an invitation linked to the attendee.');
        }

        $invitation->acceptedBy($this);

        $this->invitation = $invitation;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function getLinkedIn(): LinkedIn\Profile
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(LinkedIn\Profile $linkedIn)
    {
        $this->linkedIn = $linkedIn;
    }

    public function getAssistant(): ?Assistant
    {
        if (null === $this->assistant || $this->assistant->empty()) {
            return null;
        }

        return $this->assistant;
    }

    public function setAssistant(Assistant $assistant)
    {
        $this->assistant = $assistant;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->emailVerified;
    }

    public function markEmailAsVerified()
    {
        $this->emailVerified = true;
    }

    public function getSentInvitations(): ?iterable
    {
        return $this->sentInvitations;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture)
    {
        $this->picture = $picture;
    }

    public function getTripDetails(): ?TripDetails
    {
        return $this->tripDetails;
    }

    public function setTripDetails(TripDetails $tripDetails)
    {
        $this->tripDetails = $tripDetails;
    }

    public function getStatusHistory(): array
    {
        $history = [];

        foreach ($this->statusHistory as $row) {
            list($status, $dateStr) = $row;
            $date                   = new DateTimeImmutable($dateStr);

            if (false === isset($history[$status]) || $history[$status] < $date) {
                $history[$status] = $date;
            }
        }

        asort($history);

        return $history;
    }

    private function pushStatusHistory(string $status)
    {
        if (false === empty($this->statusHistory)) {
            $last                       = end($this->statusHistory);
            list($lastStatus, $dateStr) = $last;

            if ($status === $lastStatus) {
                return;
            }
        }

        $this->statusHistory[] = [
            $status,
            date('c'),
        ];
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return <<<'PHP'
<?php

namespace Domain\Model;

use DateTimeImmutable;
use Domain\Uuid;
use LogicException;
use Webmozart\Assert\Assert;

class Attendee
{
    const TYPE_ASSOCIATION = 'association';
    const TYPE_JOURNALIST  = 'journalist';
    const TYPE_OWNER       = 'owner';
    const TYPE_STAFF       = 'staff';
    const TYPE_OTHER_GUEST = 'other_guest';
    const TYPE_RETAILER    = 'retailer';
    const TYPE_SPONSOR     = 'sponsor';

    const STATUS_INCOMPLETE   = 'incomplete';
    const STATUS_PENDING      = 'pending';
    const STATUS_ACCEPTED     = 'accepted';
    const STATUS_DECLINED     = 'declined';
    const STATUS_WAITING_LIST = 'waiting list';
    const STATUS_CONFIRMED    = 'confirmed';
    const STATUS_CANCELLED    = 'cancelled';

    const PASS_MEMBER   = 'member';
    const PASS_ATTENDEE = 'attendee';
    const PASS_1_TO_1   = '1_to_1';

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $givenName;

    /**
     * @var string
     */
    private $familyName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var null|string
     */
    private $password;

    /**
     * @var Telephone
     */
    private $telephone;

    /**
     * @var string
     */
    private $status;

    /**
     * @var DateTimeImmutable
     */
    private $registeredAt;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string[]
     */
    private $countriesInCharge;

    /**
     * @var string[]
     */
    private $areasOfExpertise;

    /**
     * @var LinkedIn\Profile
     */
    private $linkedIn;

    /**
     * @var null|string
     */
    private $passType;

    /**
     * @var null|string
     */
    private $jobTitle;

    /**
     * @var null|string
     */
    private $industry;

    /**
     * @var null|string
     */
    private $seniority;

    /**
     * @var null|string
     */
    private $challenge;

    /**
     * @var Organisation
     */
    private $workForOrganisation;

    /**
     * @var Sponsor
     */
    private $workForSponsor;

    /**
     * @var Invitation
     */
    private $invitation;

    /**
     * @var null|Assistant
     */
    private $assistant;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $emailVerified;

    /**
     * @var Invitation[]
     */
    private $sentInvitations;

    /**
     * @var string | null
     */
    private $picture;

    /**
     * @var null|TripDetails
     */
    private $tripDetails;

    /**
     * @var array
     */
    private $statusHistory = [];

    private function __construct(
        string $type,
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $picture = null,
        string $country = null,
        array  $countriesInCharge = null,
        $workFor = null,
        array  $areasOfExpertise = null,
        string $status = self::STATUS_ACCEPTED,
        ?string $passType = self::PASS_ATTENDEE,
        string $jobTitle = null,
        string $industry = null,
        string $seniority = null,
        string $challenge = null,
        Assistant $assistant = null,
        TripDetails $tripDetails = null
    ) {
        $this->type  = $type;
        $this->email = $email;

        $this->givenName         = $givenName;
        $this->familyName        = $familyName;
        $this->password          = null;
        $this->telephone         = $telephone;
        $this->picture           = $picture;
        $this->country           = $country;
        $this->countriesInCharge = $countriesInCharge ?: [];
        $this->areasOfExpertise  = $areasOfExpertise ?: [];
        $this->status            = $status;
        $this->passType          = $passType;
        $this->jobTitle          = $jobTitle;
        $this->industry          = $industry;
        $this->seniority         = $seniority;
        $this->challenge         = $challenge;
        $this->assistant         = $assistant;
        $this->tripDetails       = $tripDetails;

        $this->identifier    = Uuid::generate();
        $this->password      = null;
        $this->registeredAt  = new DateTimeImmutable();
        $this->linkedIn      = new LinkedIn\Profile();
        $this->emailVerified = false;

        $this->pushStatusHistory($status);

        Assert::oneOf($type, [
            self::TYPE_ASSOCIATION,
            self::TYPE_JOURNALIST,
            self::TYPE_OWNER,
            self::TYPE_STAFF,
            self::TYPE_OTHER_GUEST,
            self::TYPE_RETAILER,
            self::TYPE_SPONSOR,
        ]);

        Assert::oneOf($passType, [
            self::PASS_MEMBER,
            self::PASS_ATTENDEE,
            self::PASS_1_TO_1,
        ]);

        if (null === $workFor) {
            return;
        }

        if ($workFor instanceof Organisation) {
            $this->workForOrganisation = $workFor;

            return;
        }

        if ($workFor instanceof Sponsor) {
            $this->workForSponsor = $workFor;

            return;
        }

        throw new LogicException('$workFor have to be an Organisation or a Sponsor');
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName)
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName)
    {
        $this->familyName = $familyName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        if ($email !== $this->email) {
            $this->emailVerified = false;
        }

        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getTelephone(): ?Telephone
    {
        if (null === $this->telephone) {
            return null;
        }

        return empty($this->telephone->getRegion()) || empty($this->telephone->getNationalNumber())
            ? null
            : $this->telephone;
    }

    public function setTelephone(Telephone $telephone)
    {
        $this->telephone = $telephone;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        $this->pushStatusHistory($status);
    }

    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    public function getCountriesInCharge(): array
    {
        return $this->countriesInCharge;
    }

    public function setCountriesInCharge(array $countriesInCharge)
    {
        $this->countriesInCharge = $countriesInCharge;
    }

    public function getAreasOfExpertise(): array
    {
        return $this->areasOfExpertise;
    }

    public function setAreasOfExpertise(array $areasOfExpertise)
    {
        $this->areasOfExpertise = $areasOfExpertise;
    }

    public function getLinkedIn(): LinkedIn\Profile
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(LinkedIn\Profile $linkedIn)
    {
        $this->linkedIn = $linkedIn;
    }

    public function getPassType(): ?string
    {
        return $this->passType;
    }

    public function setPassType(string $passType = self::PASS_MEMBER)
    {
        Assert::oneOf($passType, [
            self::PASS_MEMBER,
            self::PASS_ATTENDEE,
            self::PASS_1_TO_1,
        ]);

        $this->passType = $passType;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    public function getIndustry(): ?string
    {
        return $this->industry;
    }

    public function setIndustry(string $industry)
    {
        $this->industry = $industry;
    }

    public function getSeniority(): ?string
    {
        return $this->seniority;
    }

    public function setSeniority(string $seniority)
    {
        $this->seniority = $seniority;
    }

    public function getChallenge(): ?string
    {
        return $this->challenge;
    }

    public function setChallenge(string $challenge)
    {
        $this->challenge = $challenge;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function getAssistant(): ?Assistant
    {
        if (null === $this->assistant || $this->assistant->empty()) {
            return null;
        }

        return $this->assistant;
    }

    public function setAssistant(Assistant $assistant)
    {
        $this->assistant = $assistant;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSentInvitations(): ?iterable
    {
        return $this->sentInvitations;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture)
    {
        $this->picture = $picture;
    }

    public function getTripDetails(): ?TripDetails
    {
        return $this->tripDetails;
    }

    public function setTripDetails(TripDetails $tripDetails)
    {
        $this->tripDetails = $tripDetails;
    }

    public function getStatusHistory(): array
    {
        $history = [];

        foreach ($this->statusHistory as $row) {
            list($status, $dateStr) = $row;
            $date                   = new DateTimeImmutable($dateStr);

            if (false === isset($history[$status]) || $history[$status] < $date) {
                $history[$status] = $date;
            }
        }

        asort($history);

        return $history;
    }

    public static function createASponsor(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        array $countriesInCharge = null,
        Sponsor $workFor = null,
        string $jobTitle = null,
        string $picture = null,
        array  $areasOfExpertise = [],
        string $passType = self::PASS_MEMBER,
        Assistant $assistant = null
    ) {
        return new self(
            self::TYPE_SPONSOR,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            $countriesInCharge,
            $workFor,
            $areasOfExpertise,
            self::STATUS_ACCEPTED,
            $passType,
            $jobTitle,
            null,
            null,
            null,
            $assistant
        );
    }

    public static function createARetailer(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $picture = null,
        string $country = null,
        array $countriesInCharge = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $industry = null,
        string $seniority = null,
        string $challenge = null,
        array $areasOfExpertise = null,
        Assistant $assistant = null
    ) {
        return new self(
            self::TYPE_RETAILER,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            $countriesInCharge,
            $workFor,
            $areasOfExpertise,
            self::STATUS_INCOMPLETE,
            self::PASS_ATTENDEE,
            $jobTitle,
            $industry,
            $seniority,
            $challenge,
            $assistant
        );
    }

    public static function createAStaff(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_STAFF,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_CONFIRMED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public static function createAnAssociation(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_ASSOCIATION,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_ACCEPTED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public static function createAJournalist(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_JOURNALIST,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_ACCEPTED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public static function createAnOtherGuest(
        string $email,
        string $givenName = null,
        string $familyName = null,
        Telephone $telephone = null,
        string $country = null,
        Organisation $workFor = null,
        string $jobTitle = null,
        string $picture = null
    ) {
        return new self(
            self::TYPE_OTHER_GUEST,
            $email,
            $givenName,
            $familyName,
            $telephone,
            $picture,
            $country,
            [],
            $workFor,
            [],
            self::STATUS_ACCEPTED,
            self::PASS_ATTENDEE,
            $jobTitle
        );
    }

    public function isStaff(): bool
    {
        return in_array($this->type, [
            self::TYPE_OWNER,
            self::TYPE_STAFF,
        ]);
    }

    public function isOwner(): bool
    {
        return self::TYPE_OWNER === $this->type;
    }

    public function isEqualTo(self $other): bool
    {
        return $other->getIdentifier() === $this->getIdentifier();
    }

    /**
     * @return null|Organisation|Sponsor
     */
    public function getWorkFor()
    {
        if (null !== $this->workForSponsor) {
            return $this->workForSponsor;
        }

        if (null !== $this->workForOrganisation && !$this->workForOrganisation->empty()) {
            return $this->workForOrganisation;
        }
    }

    public function setWorkFor(Organisation $workFor)
    {
        $this->workForOrganisation = $workFor;
    }

    public function attachToInvitation(Invitation $invitation)
    {
        if (null !== $this->invitation) {
            throw new LogicException('There is already an invitation linked to the attendee.');
        }

        $invitation->acceptedBy($this);

        $this->invitation = $invitation;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->emailVerified;
    }

    public function markEmailAsVerified()
    {
        $this->emailVerified = true;
    }

    private function pushStatusHistory(string $status)
    {
        if (false === empty($this->statusHistory)) {
            $last                       = end($this->statusHistory);
            list($lastStatus, $dateStr) = $last;

            if ($status === $lastStatus) {
                return;
            }
        }

        $this->statusHistory[] = [
            $status,
            date('c'),
        ];
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 70100;
    }
}
