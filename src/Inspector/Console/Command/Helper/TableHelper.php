<?php

namespace Inspector\Console\Command\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;

class TableHelper extends Helper
{
    private $head   = array();
    private $body   = array();
    private $widths = array();
    private $cacheRowCount = 0;

    public function setHead(array $columns)
    {
        $this->head = $columns;
    }

    public function setBody(array $rows)
    {
        $this->body = $rows;
    }

    public function addRow(array $columns)
    {
        $this->body[] = $columns;
    }

    public function render(OutputInterface $output)
    {
        $widths = $this->calculateColumnWidth();
        $i = 0;
        $head = '';
        $headLine = '';

        // head
        foreach ($this->head as $headColumn) {
            $headLen = strlen($headColumn);
            $widths[$i] = ($widths[$i] < $headLen
                    ? $headLen
                    : $widths[$i]
            );

            $head .= $headColumn.str_repeat(' ', $widths[$i] + 2 - $headLen);
            $headLine .= str_repeat('=', $widths[$i]).'  ';

            $i++;
        }
        $output->writeln(array(
            '<info>'.$head.'</info>',
            '<comment>'.$headLine.'</comment>',
            '',
        ));

        // body
        $body = array_map(function ($row) use ($widths) {
            $rowLine = array();

            $i = 0;
            foreach ($row as $column) {
                $columnLen = strlen($column);
                if ($columnLen < $widths[$i]) {
                    $rowLine[] = $column.str_repeat(' ', $widths[$i] - $columnLen);
                } else {
                    $rowLine[] = $column;
                }
            }

            return implode('  ', $rowLine);
        }, $this->body);
        $output->writeln($body);
    }

    protected function calculateColumnWidth($force_calculate = false)
    {
        if ((null === $this->widths) || $this->cacheRowCount !== ($c = count($this->body))) {
            $this->cacheRowCount = $c;
            $this->doCalculateColumnWidth();
        }
        if ($force_calculate) {
            $this->doCalculatecolumnWidth();
        }

        return $this->widths;
    }

    private function doCalculateColumnWidth()
    {
        $widths = array();

        foreach ($this->body as $row) {
            $i = 0;

            foreach ($row as $column) {
                $widths[$i] = isset($widths[$i]) ? $widths[$i] : 0;

                if ($widths[$i] < strlen($column)) {
                    $widths[$i] = strlen($column);
                }

                $i++;
            }
        }

        $this->widths = $widths;
    }

    public function getName()
    {
        return 'table';
    }
}
