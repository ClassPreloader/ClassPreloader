<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClassPreloader\Commands;

use ClassPreloader\ClassPreloader;
use ClassPreloader\Exceptions\SkipFileException;
use ClassPreloader\Parser\DirVisitor;
use ClassPreloader\Parser\FileVisitor;
use ClassPreloader\Parser\NodeTraverser;
use InvalidArgumentException;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This is the pre-compile command class.
 *
 * This allows the user to communicate with class preloader.
 */
class PreCompileCommand extends Command
{
    /**
     * The input object.
     *
     * @var \Symfony\Component\Console\Input\InputInterface|null
     */
    protected $input;

    /**
     * The output object.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface|null
     */
    protected $output;

    /**
     * Configure the current command.
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('compile')
            ->setDescription('Compiles classes into a single file')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'CSV of filenames to load, or the path to a PHP script that returns an array of file names')
            ->addOption('output', null, InputOption::VALUE_REQUIRED)
            ->addOption('skip_dir_file', null, InputOption::VALUE_NONE, 'Skip files with __DIR__ or __FILE__ to make the cache portable')
            ->addOption('fix_dir', null, InputOption::VALUE_REQUIRED, 'Convert __DIR__ constants to the original directory of a file', 1)
            ->addOption('fix_file', null, InputOption::VALUE_REQUIRED, 'Convert __FILE__ constants to the original path of a file', 1)
            ->addOption('strip_comments', null, InputOption::VALUE_REQUIRED, 'Set to 1 to strip comments from each source file', 0)
            ->setHelp(<<<EOF
The <info>%command.name%</info> command iterates over each script, normalizes
the file to be wrapped in namespaces, and combines each file into a single PHP
file.
EOF
        );
    }

    /**
     * Get the node traverser used by the command.
     *
     * @return \ClassPreloader\Parser\NodeTraverser
     */
    protected function getTraverser()
    {
        $traverser = new NodeTraverser();

        if ($this->input->getOption('fix_dir')) {
            $traverser->addVisitor(new DirVisitor($this->input->getOption('skip_dir_file')));
        }

        if ($this->input->getOption('fix_file')) {
            $traverser->addVisitor(new FileVisitor($this->input->getOption('skip_dir_file')));
        }

        return $traverser;
    }

    /**
     * Validate the command options.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function validateCommand()
    {
        if (!$this->input->getOption('output')) {
            throw new InvalidArgumentException('An output option is required');
        }

        if (!$this->input->getOption('config')) {
            throw new InvalidArgumentException('A config option is required');
        }
    }

    /**
     * Executes the pre-compile command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->validateCommand();

        $preloader = new ClassPreloader(new PrettyPrinter(), new Parser(new Lexer()), $this->getTraverser());

        $this->output->writeln('> Loading configuration file');
        $config = $this->input->getOption('config');
        $files = $preloader->getFileList($config);
        $output->writeLn('- Found '.count($files).' files');

        $outputFile = $this->input->getOption('output');
        $handle = $preloader->prepareOutput($outputFile);

        $output->writeln('> Compiling classes');

        $count = 0;
        $countSkipped = 0;
        $comments = !$this->input->getOption('strip_comments');

        foreach ($files as $file) {
            $count++;
            try {
                $code = $preloader->getCode($file, $comments);
                $this->output->writeln('- Writing '.$file);
                fwrite($handle, $code."\n");
            } catch (SkipFileException $ex) {
                $countSkipped++;
                $this->output->writeln('- Skipping '.$file);
            }
        }

        fclose($handle);

        $output->writeln("> Compiled loader written to $outputFile");
        $output->writeln('- Files: '.($count - $countSkipped).'/'.$count.' (skipped: '.$countSkipped.')');
        $output->writeln('- Filesize: '.(round(filesize($outputFile) / 1024)).' kb');
    }
}
