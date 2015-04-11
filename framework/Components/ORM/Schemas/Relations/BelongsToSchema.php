<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 * @copyright ©2009-2015
 */
namespace Spiral\Components\ORM\Schemas\Relations;

use Spiral\Components\ORM\Entity;
use Spiral\Components\ORM\ORMException;
use Spiral\Components\ORM\Schemas\RelationSchema;

class BelongsToSchema extends RelationSchema
{
    /**
     * Relation type.
     */
    const RELATION_TYPE = Entity::BELONGS_TO;

    /**
     * Equivalent relationship resolved based on definition and not schema, usually polymorphic.
     */
    const EQUIVALENT_RELATION = Entity::BELONGS_TO_MORPHED;

    /**
     * Default definition parameters, will be filled if parameter skipped from definition by user.
     *
     * @invisible
     * @var array
     */
    protected $defaultDefinition = array(
        Entity::INNER_KEY         => '{outer:roleName}_{outer:primaryKey}',
        Entity::CONSTRAINT        => true,
        Entity::CONSTRAINT_ACTION => 'CASCADE'
    );

    /**
     * Create all required relation columns, indexes and constraints.
     */
    public function buildSchema()
    {
        $innerSchema = $this->entitySchema->getTableSchema();

        $innerKey = $innerSchema->column($this->definition[Entity::INNER_KEY]);
        $innerKey->type($this->outerEntity()->getPrimaryAbstractType());
        $innerKey->nullable(true);
        $innerKey->index();

        if ($this->definition[Entity::CONSTRAINT])
        {
            $foreignKey = $innerKey->foreign(
                $this->outerEntity()->getTable(),
                $this->outerEntity()->getPrimaryKey()
            );
            $foreignKey->onDelete($this->definition[Entity::CONSTRAINT_ACTION]);
            $foreignKey->onUpdate($this->definition[Entity::CONSTRAINT_ACTION]);
        }
    }

    /**
     * Create reverted relations in outer entity or entities.
     *
     * @param string $name Relation name.
     * @param int    $type Back relation type, can be required some cases.
     * @throws ORMException
     */
    public function revertRelation($name, $type = null)
    {
        if (empty($type))
        {
            throw new ORMException(
                "Unable to revert BELONG_TO relation ({$this->entitySchema}), " .
                "back relation type is missing."
            );
        }

        $this->outerEntity()->addRelation($name, array(
            $type                     => $this->entitySchema->getClass(),
            Entity::OUTER_KEY         => $this->definition[Entity::INNER_KEY],
            Entity::CONSTRAINT        => $this->definition[Entity::CONSTRAINT],
            Entity::CONSTRAINT_ACTION => $this->definition[Entity::CONSTRAINT_ACTION]
        ));
    }
}