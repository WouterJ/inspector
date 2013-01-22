<?php

namespace Inspector\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Inspector\Exception;
use Inspector\Util\MatchUtil;

/**
 * The main command of inspector.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class InspectorCommand extends Command
{
    /**
     * {@inheritDoc}
     */
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
                new InputOption('filter', 'f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A pattern that defines which files to ignore'),
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getContainer();
        $table = $this->getHelperSet()->get('table');

        // set up
        if (null === $input->getOption('pattern')) {
            throw new Exception\OptionNotProvidedException('pattern');
        }

        if (null === $input->getOption('dir')) {
            $input->setOption('dir', getcwd());
        }

        // filter listener
        $filter = $input->getOption('filter');
        $inspectorFilters = array();
        $listenerFilters = array();

        if (is_array($filter)) {
            foreach ($filter as $f) {
                if (MatchUtil::isMatch($f)) {
                    $inspectorFilters[] = $f;
                } else {
                    $listenerFilters[] = $f;
                }
            }
        } else {
            if (MatchUtil::isMatch($filter)) {
                $inspectorFilters[] = $f;
            } else {
                $listenerFilters[] = $f;
            }
        }

        $container['inspector.filter_listener']->setFilters($listenerFilters);

        // inspector
        $inspector = $container['inspector'];

        $suspects = $inspector->inspect($input->getOption('dir'), $input->getOption('pattern'), $inspectorFilters);

        $table->setHead(array('id', 'file'));
        $j = 1;
        $rows = array_map(function ($suspect) use (&$j) {
            return array($j++, $suspect->getRelativePathName());
        }, $suspects->getArrayCopy());
        $table->setBody($rows);

        $table->render($output);
    }

    /**
     * @param string|array $filters An array with filters (e.g. `array('*.php', 'test/*')`) or a string (e.g. `'*.php test/.*'`)
     *
     * @return array
     */
    private function parseFilters($filter)
    {
        if (is_array($filter)) {
            return $filter;
        }

        return explode(' ', $filter);
    }
}
