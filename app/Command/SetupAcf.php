<?php

/**
 * Setup for ACF commands
 *
 * @package studiometa/merlin
 */

namespace Merlin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class SetupAcf extends Command
{
    /**
     * Command Name
     *
     * @var string
     */
    protected static $defaultName = 'acf:setup';

    /**
     * SymfonyStyle
     *
     * @var SymfonyStyle
     */
    protected $io = null;

    /**
     * The prompt data, use to create the field group file class.
     *
     * @var array
     */
    protected $config = [];

    /**
     * project directory
     *
     * @var string
     */
    private $project_dir;

    /**
     * contruct
     *
     * @param string $project_dir
     */
    public function __construct($project_dir)
    {
        $this->project_dir = $project_dir;
        parent::__construct();
    }

    /**
     * Configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Setup for other ACF commands');
    }

     /**
     * Interact function
     *
     * @param InputInterface  $input Input.
     * @param OutputInterface $output Output.
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Setup ACF config');
        $this->config['theme'] = $this->io->ask('Enter the absolute path of your WordPress theme');
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
            file_put_contents($this->project_dir . '/config/config.yml', Yaml::dump($this->config));
            return Command::SUCCESS;
        }

        $this->io->caution('Roger that ! Abort mission !');
        return Command::FAILURE;
    }
}
