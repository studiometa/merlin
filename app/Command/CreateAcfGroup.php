<?php

/**
 * Create ACF Field Group command
 *
 * @package studiometa/merlin
 */

namespace Studiometa\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * # Add ACF Fields Group via Symfony Console Command
 *
 * - command component
 * - question helper
 *
 * 1. Field group name
 * 2. Field group location
 *   2.1 (One step ? Multiple Step ? Might need to add a link to explain what you need to fill ?)
 *   2.1 WordPress content type / equality / Matching rule
 * 3. Field type to add
 *   3.1 list from: image, text, wysiwyg, relation, link, page link, gallery, repeater
 *   3.3 Field name
 *   3.3 If repeater handle subfields
 *     3.3.1 Go back to 3 for subfields
 *   3.4 Do you want to add another field y/n (Recursive fn that goes back to 2)
 * 4. Recap to view what you are creating
 * 5. Create the field group y/n
 * 6. Create a file filled with all the data in the prompt
 *
 * - Do not handle custom field configuration in the first version
 * - Do not handle multiple locations for field group
 */
class CreateAcfGroup extends Command
{

    /**
     * Command Name
     *
     * @var string
     */
    protected static $defaultName = 'acf:create';

    /**
     * The prompt data, use to create the field group file class.
     *
     * @var array
     */
    protected $data = [
        'fields' => [],
    ];

    /**
     * SymfonyStyle
     *
     * @var SymfonyStyle
     */
    protected $io = null;


    /**
     * The type of fields available to add in a field group.
     * @see https://www.advancedcustomfields.com/resources/#field-types
     * @var array
     */
    protected $field_types = [
        'text',
        'wysiwyg',
        'image',
        'relation',
        'link',
        'page link',
        'gallery',
    ];

    /**
     * Configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Create an ACF field group class');
    }

    /**
     * Interact function
     *
     * @param InputInterface  $input Input.
     * @param OutputInterface $output Output.
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        // @todo add a verification of the content of config file, else ask user to run acf:setup

        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Welcome to the ACF Field Group Generator');
        $this->io->note('Add usefull tips here');

        $this->io->section('1. Field Group informations');

        // @todo add a validator to only allow lowercase, trim space, etc
        $this->data['slug'] = $this->io->ask('Enter the name of the field group to create');

        // Field group location location.
        $this->io->section('2. Field Group location');
        $this->io->note('Exemple: post_type == product');

        $this->data['location']['when']  = $this->io->choice(
            'Show this field group if',
            [
                'post_type',
                'post_template',
                'post_status',
                'post_format',
                'post_category',
                'post_taxonomy',
                'page_template',
                'page_type',
                'page_parent',
                'page',
                'current_user',
            ]
        );
        $this->data['location']['equal'] = $this->io->choice(
            $this->data['location']['when'] . ' is equal to/not equal to',
            ['==', '!=']
        );
        $this->data['location']['value'] = $this->io->ask('Show this group when ' .
            $this->data['location']['when'] . ' ' . $this->data['location']['equal']);

        $this->io->newLine(2);

        $this->io->section('3. Add fields to the group');

        $this->addField();

        $this->io->section('4. Recap');
        $this->io->text('Field group name :' . $this->data['slug']);
        $this->io->text('Field group location :' . $this->data['location']['when'] . ' '
            . $this->data['location']['equal'] . ' ' . $this->data['location']['value']);
        $this->io->newLine(1);
        $this->io->text('Fields');
        $this->io->table(['type', 'slug', 'label', 'required'], $this->data['fields']);
    }

    /**
     * Add a field
     */
    public function addField(): void
    {
        $field_type  = $this->io->choice('Add a field', $this->field_types);
        $field_slug  = $this->io->ask('Enter the slug of the field');
        $field_label = $this->io->ask('Enter the label of the field');
        $required    = $this->io->choice('Is the field required ', ['no', 'yes']);

        $this->data['fields'][] = [
            'type'     => $field_type,
            'slug'     => $field_slug,
            'label'    => $field_label,
            'required' => $required,
        ];

        $add_another = $this->io->confirm('Do you want to add another field ? ', true);

        if ($add_another) {
            $this->addField();
        }
    }

    /**
     * Execute command function
     *
     * @param InputInterface  $input input.
     * @param OutputInterface $output output.
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $confirm = $this->io->confirm('Continue with this action ? ', true);

        if ($confirm) {
            // @todo Add creation of the templates here, use $this->data
            return Command::SUCCESS;
        }

        $this->io->caution('Roger that ! Abort mission !');
        return Command::FAILURE;
    }
}
