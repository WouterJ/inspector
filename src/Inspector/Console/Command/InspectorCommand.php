<?php

namespace Inspector\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InspectorCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('inspect')
            ->setDescription('Searches in a directory')
            ->setHelp(<<<EOT
The <info>inspect</info> command searches in the given directory for 
a pattern.

The command has 1 required option, <comment>--pattern</comment> (<comment>-p</comment>).
This will specify which pattern should be matched.

<info>php inspector inspect -p Foobar</info>

By default, the current directory is used. You can change
this by using the <comment>--dir</comment> (<comment>-d</comment>) option.
EOT
            )
            ->setDefinition(array(
                new InputOption('pattern', 'p', InputOption::VALUE_REQUIRED, 'The pattern to search for, this can be a string or a pattern'),
                new InputOption('dir', 'd', InputOption::VALUE_REQUIRED, 'The directory to search in, this will be the current directory by default'),
            ))
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $this->getHelperSet()->get('table');

        // set up
        if (null === $input->getOption('pattern')) {
            throw new \RunTimeException('The "pattern" option must be provided');
        }

        if (null === $input->getOption('dir')) {
            $input->setOption('dir', getcwd());
        }

        // inspector
        $container = $this->getApplication()->getContainer();
        $inspector = $container['inspector'];

        $suspects = $inspector->inspect($input->getOption('dir'), $input->getOption('pattern'));

        $table->setHead(array('id', 'file'));
        $j = 0;
        $rows = array_map(function ($i) use (&$j) {
            return array($j++, $i);
        }, $suspects->getArrayCopy());
        $table->setBody($rows);

        $table->render($output);
    }
}
