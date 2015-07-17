<?php

namespace BAP\SimpleBTSBundle\Entity;

use BAP\SimpleBTSBundle\Model\ExtendIssue;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

use Oro\Bundle\TagBundle\Entity\Tag;
use Oro\Bundle\TagBundle\Entity\Taggable;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Issue
 *
 * @ORM\Table(name="bts_issue", indexes={
 *     @ORM\Index(name="bts_issue_updated_at_idx", columns={"updatedAt"}),
 *     @ORM\Index(name="bts_issue_created_at_idx", columns={"createdAt"}),
 *     @ORM\Index(name="bts_issue_summary_idx", columns={"summary"}),
 *     @ORM\Index(name="bts_issue_code_idx", columns={"code"}),
 * })
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="BAP\SimpleBTSBundle\Entity\Repository\IssueRepository")
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-tasks"
 *          }
 *      }
 * )
 */
class Issue extends ExtendIssue implements Taggable
{
    const TYPE_BUG      = 'bug';
    const TYPE_SUBTASK  = 'subtask';
    const TYPE_TASK     = 'task';
    const TYPE_STORY    = 'story';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255)
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    protected $type;

    /**
     * @var Tag[]
     */
    protected $tags;

    /**
     * @var IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="IssuePriority")
     */
    protected $priority;

    /**
     * @var IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="IssueResolution")
     */
    protected $resolution;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reporter;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $assignee;

    /**
     * @var ArrayCollection|Issue[]
     *
     * @ORM\ManyToMany(targetEntity="Issue", inversedBy="relatedToIssues")
     * @ORM\JoinTable(name="bts_issue2issue")
     **/
    protected $relatedIssues;

    /**
     * @var ArrayCollection|Issue[]
     *
     * @ORM\ManyToMany(targetEntity="Issue", mappedBy="relatedIssues")
     **/
    protected $relatedToIssues;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="bts_issue2collaborator",
     *     joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="collaborator_id", referencedColumnName="id")}
     * )
     **/
    protected $collaborators;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     **/
    protected $children;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(type="datetime")
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          }
     *      }
     * )
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(type="datetime")
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          }
     *      }
     * )
     */
    protected $updatedAt;

    public function __construct()
    {
        parent::__construct();

        $this->tags = new ArrayCollection();
        $this->relatedIssues = new ArrayCollection();
        $this->relatedToIssues = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Issue
     */
    public function setType($type)
    {
        if (! in_array($type, [
            self::TYPE_BUG,
            self::TYPE_STORY,
            self::TYPE_SUBTASK,
            self::TYPE_TASK
        ])) {
            throw new \InvalidArgumentException('Invalid issue type: "' . $type . '"');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the unique taggable resource identifier
     *
     * @return string
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * Returns the collection of tags for this Taggable entity
     *
     * @return ArrayCollection|Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set tag collection
     *
     * @param ArrayCollection|Tag[] $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @param User $assignee
     * @return $this
     */
    public function setAssignee(User $assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param User $reporter
     * @return $this
     */
    public function setReporter(User $reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Issue
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Issue
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add relatedIssues
     *
     * @param Issue $relatedIssue
     * @return Issue
     */
    public function addRelatedIssue(Issue $relatedIssue)
    {
        $this->relatedIssues[] = $relatedIssue;

        return $this;
    }

    /**
     * Remove relatedIssues
     *
     * @param Issue $relatedIssue
     */
    public function removeRelatedIssue(Issue $relatedIssue)
    {
        $this->relatedIssues->removeElement($relatedIssue);
    }

    /**
     * Get relatedIssues
     *
     * @return ArrayCollection|Issue[]
     */
    public function getRelatedIssues()
    {
        return $this->relatedIssues;
    }

    /**
     * Add relatedToIssues
     *
     * @param Issue $relatedToIssue
     * @return Issue
     */
    public function addRelatedToIssue(Issue $relatedToIssue)
    {
        $this->relatedToIssues[] = $relatedToIssue;

        return $this;
    }

    /**
     * Remove relatedToIssues
     *
     * @param Issue $relatedToIssue
     */
    public function removeRelatedToIssue(Issue $relatedToIssue)
    {
        $this->relatedToIssues->removeElement($relatedToIssue);
    }

    /**
     * Get relatedToIssues
     *
     * @return ArrayCollection|Issue[]
     */
    public function getRelatedToIssues()
    {
        return $this->relatedToIssues;
    }

    /**
     * Add collaborators
     *
     * @param User $collaborator
     * @return Issue
     */
    public function addCollaborator(User $collaborator)
    {
        $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborators
     *
     * @param User $collaborator
     */
    public function removeCollaborator(User $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Get collaborators
     *
     * @return ArrayCollection|User[]
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set parent
     *
     * @param Issue $parent
     * @return Issue
     */
    public function setParent(Issue $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param Issue $child
     * @return Issue
     */
    public function addChild(Issue $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Issue $child
     */
    public function removeChild(Issue $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return ArrayCollection|Issue[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Pre persist event listener
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt  = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt  = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Invoked before the entity is updated.
     *
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Set priority
     *
     * @param IssuePriority $priority
     * @return Issue
     */
    public function setPriority(IssuePriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resolution
     *
     * @param IssueResolution $resolution
     * @return Issue
     */
    public function setResolution(IssueResolution $resolution = null)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return IssueResolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }
}
