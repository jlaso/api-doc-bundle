<?php

namespace JLaso\ApiDocBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;


/**
 * @author Joseluis Laso <jlaso@joseluislaso.es>
 */
class GenerateDocCommand extends ContainerAwareCommand
{
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;

    private $srcDir;

    const THROWS_EXCEPTION = true;

    /** @var array */
    protected $inputFiles = array();

    /** @var array */
    protected $data = array();
    /** @var array */
    protected $filterStore = array();

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('jlaso:api-doc:generate');
        $this->setDescription('Generate de documentation of the API through the annotations found.');
    }

    protected function init()
    {
        $this->srcDir = realpath($this->getApplication()->getKernel()->getRootDir() . '/../');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->init();

        $this->output->writeln('<info>*** Generating API doc ***</info>');

        $fileNames = array();
        $keys = array();
        $idx = 0;
        $numKeys = 0;

        $patterns = array(
            '*.php'  => '/trans\s*\(\s*(["\'])(?<trans>(?:\\\1|(?!\1).)*?)\1\s*\)/i',
        );
        $folders  = array(
            $this->srcDir . '/app',
            $this->srcDir . '/src'
        );

        $keyInfo = array();

        foreach($patterns as $filePattern=>$exrPattern){

            foreach($folders as $folder){

                $output->writeln($folder);
                $finder = new Finder();
                $files = $finder->in($folder)->name($filePattern)->files();

                /** @var SplFileInfo[] $files */
                foreach($files as $file){
                    $fileName = $folder . '/' . $file->getRelativePathname();
                    if(strpos($fileName, $folder . "/cache") === 0){
                        continue;
                    }
                    if(preg_match("/\/(?P<bundle>.*Bundle)\//U", $file->getRelativePathname(), $match)){
                        $bundleName = $match['bundle'];
                    }else{
                        $bundleName = "*app";
                    }

                    $fileContents = file_get_contents($fileName);

                }
            }
        }

        $output->writeln(sprintf("Total %d files examined, and found annotations in %d files", $idx, count($fileNames)));
    }

    /**
     * @param string $message
     * @throws \Exception
     */
    protected function throwException($message = null)
    {
        if (self::THROWS_EXCEPTION) {
            throw new \Exception($message ?: 'Unexpected exception');
        }
    }


}