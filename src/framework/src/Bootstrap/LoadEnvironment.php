<?php

namespace Framework\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Repository\RepositoryBuilder;
//use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class LoadEnvironment
 * Loads the configuration variables for use
 * within the application
 */
class LoadEnvironment
{
    /**
     * @var string
     */
    protected string $filePath;

    /**
     * @var array
     */
    protected array $files;

    /**
     * LoadEnvironment constructor
     *
     * @param string $filePath
     * @param array $files
     */
    public function __construct(
        string $filePath,
        array $files = []
    ) {
        $this->filePath = $filePath;
        $this->files = $files;
    }

    /**
     * @return void
     */
     public function bootstrap(): void
    {
        try {
            $this->createDotenv()->safeLoad();
            if (!empty($this->files)) {
                foreach ($this->files as $fileName) {
                    $this->createDotenv($fileName)->safeLoad();
                }
            }
        } catch (InvalidFileException $e) {
            $this->writeErrorAndDie([
                'The environment file is invalid!',
                $e->getMessage(),
            ]);
        }
    }

    /**
     * @param string|null $fileName
     * @return Dotenv
     */
    public function createDotenv(
        ?string $fileName = null
    ): Dotenv {
        $repository = RepositoryBuilder::createWithDefaultAdapters()
            ->make();

        return Dotenv::create(
            $repository,
            $this->filePath,
            $fileName
        );
    }

    /**
     * Write the error information to the screen and exit.
     *
     * @param  string[]  $errors
     * @return void
     */
    protected function writeErrorAndDie(array $errors)
    {
        /*$output = (new ConsoleOutput)->getErrorOutput();

        foreach ($errors as $error) {
            $output->writeln($error);
        }*/

        exit(1);
    }
}