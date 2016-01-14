<?php

namespace JLaso\ApiDocBundle\Command;

use JLaso\ApiDocBundle\Service\Extractor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        //$this->addOption('assets', null, InputOption::VALUE_REQUIRED, '--assets=folder where assets are, example:favicon.ico');
        //$this->addOption('output', null, InputOption::VALUE_REQUIRED, '--output=folder where to put the documentation');
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
        $fileNames = [];

        $patterns = [
            '*.php'  => '/trans\s*\(\s*(["\'])(?<trans>(?:\\\1|(?!\1).)*?)\1\s*\)/i',
        ];
        $folders  = [
            $this->srcDir . '/app',
            $this->srcDir . '/src'
        ];

        $classes = [];

        foreach($patterns as $filePattern=>$exrPattern){

            foreach($folders as $folder){

                $output->writeln(" folder ---  {$folder} ---");
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
                    $output->writeln("processing file {$fileName} ...");
                    $fileContents = file_get_contents($fileName);

                    if (preg_match_all("/\snamespace\s+(?P<namespace>[^;]+)/i", $fileContents, $m1, PREG_SET_ORDER) &&
                        preg_match_all("/\sclass\s+(?P<class>\w+)/i", $fileContents, $m2, PREG_SET_ORDER)) {
                        //var_dump($m1, $m2);
                        $classes[] = $m1[0]['namespace'] . "\\" . $m2[0]['class'];
                    }

                }
            }
        }

        $methodsAnnotations = [];

        foreach($classes as $class) {
            $annotations = Extractor::getAllClassAnnotations($class);
            foreach ($annotations[$class] as $methodName=>$methodAnnotations) {
                if (count($methodAnnotations) > 0) {
                    $methodsAnnotations[$class][$methodName] = $methodAnnotations;
                }
            }
        }

        print_r($methodsAnnotations);

        $templating = $this->getContainer()->get('templating');
        $templating->render("JLasoApiDocBundle::index.html.twig", [
            'title' => $title,
        ]);

        $output->writeln(sprintf("Total %d files examined, and found annotations in %d files", count($classes), count($fileNames)));
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

    protected function extractAnnotations($classes)
    {
        $output = [];
        foreach ($classes as $class) {
            $output[] = Extractor::getAllClassAnnotations($class);
        }

        return $output;
    }

}