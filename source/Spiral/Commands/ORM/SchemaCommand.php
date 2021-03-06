<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Commands\ORM;

use Spiral\Commands\ORM\Helpers\MigrationHelper;
use Spiral\Console\Command;
use Spiral\Debug\Benchmarker;
use Spiral\ORM\ORM;
use Spiral\ORM\Schemas\SchemaBuilder;
use Symfony\Component\Console\Input\InputOption;

/**
 * Performs ODM schema update and binds SchemaBuilder in container.
 */
class SchemaCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    const NAME = 'orm:schema';

    /**
     * {@inheritdoc}
     */
    const DESCRIPTION = 'Update ORM schema';

    /**
     * {@inheritdoc}
     */
    const OPTIONS = [
        [
            'alter',
            'a',
            InputOption::VALUE_NONE,
            'Automatically alter databases based on declared schemas'
        ],
        [
            'migrate',
            'm',
            InputOption::VALUE_NONE,
            'Create migration to alter database schema'
        ],
    ];

    /**
     * @param Benchmarker $benchmarker
     * @param ORM         $orm
     */
    public function perform(Benchmarker $benchmarker, ORM $orm, MigrationHelper $migrationHelper)
    {
        $benchmark = $benchmarker->benchmark($this, 'update');

        $builder = $orm->schemaBuilder(true);

        //Rendering schema
        $orm->setSchema($builder->renderSchema(), true);

        $elapsed = number_format($benchmarker->benchmark($this, $benchmark), 3);

        $countModels = count($builder->getSchemas());
        $countTables = count($builder->getTables());
        $countSources = count($builder->getSources());
        $countRelations = count($builder->getRelations());

        if ($countModels > $countSources) {
            $countSources = "<fg=red>{$countSources}</fg=red>";
        } else {
            $countSources = "<comment>{$countSources}</comment>";
        }

        if ($countTables != $countModels) {
            $countTables = "<fg=cyan>{$countTables}</fg=cyan>";
        } else {
            $countTables = "<comment>{$countTables}</comment>";
        }

        $this->write("<info>ORM Schema have been updated:</info> <comment>{$elapsed} s</comment>");
        $this->write("<info>, records: </info><comment>{$countModels}</comment></info>");
        $this->write("<info>, sources: </info>{$countSources}</info>");
        $this->write("<info>, tables: </info>{$countTables}</info>");
        $this->writeln("<info>, relations: </info><comment>{$countRelations}</comment></info>");

        if (!$this->hasChanges($builder)) {
            $this->writeln("<info>No database changes are detected.</info>");

            return;
        } else {
            $this->showChanges($builder);
        }

        if ($this->option('migrate')) {
            $migration = $migrationHelper->createMigration($builder);
            $this->writeln("<info>New migration created: </info><comment>{$migration}</comment>");

            return;
        }

        if ($this->option('alter')) {
            $benchmark = $benchmarker->benchmark($this, 'update');
            $builder->pushSchema();
            $elapsed = number_format($benchmarker->benchmark($this, $benchmark), 3);

            $this->writeln("<info>Databases have been modified:</info> <comment>{$elapsed} s</comment>");

            return;
        }

        $this->writeln("<info>Silent mode on, no databases altered.</info>");
    }

    /**
     * Indication that table schemas has changed.
     *
     * @param SchemaBuilder $builder
     *
     * @return bool
     */
    private function hasChanges(SchemaBuilder $builder): bool
    {
        foreach ($builder->getTables() as $table) {
            if ($table->getComparator()->hasChanges()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $builder
     */
    protected function showChanges($builder)
    {
        foreach ($builder->getTables() as $table) {
            if ($table->getComparator()->hasChanges()) {
                $this->writeln(
                    "<fg=cyan>Table schema '<comment>{$table}</comment>' has changes.</fg=cyan>"
                );
            }
        }
    }
}