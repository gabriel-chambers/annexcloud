<?php

namespace E25m\Realm\Composer\Local;

use Exception;
use E25m\Realm\Composer\Local\Interfaces\PackageInterface;
use E25m\Realm\Composer\Local\Exceptions\PackageException;

class Package implements PackageInterface
{
    protected $name;
    protected $type;
    protected $path;
    protected $extra;
    protected $meta;
    
    /**
     * __construct
     *
     * @param String $path
     * @return void
     */
    public function __construct(String $path)
    {
        $this->path = $path;
        $this->processPackage();
    }
    
    /**
     * Get package name
     *
     * @return String
     */
    public function getName(): String
    {
        return $this->name;
    }
    
    /**
     * Get package type
     *
     * @return String
     */
    public function getType(): String
    {
        return $this->type;
    }

    
    /**
     * Get package path
     *
     * @return String
     */
    public function getPath(): String
    {
        return $this->path;
    }

    
    /**
     * Get package extra property
     *
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
    
    /**
     * Read the composer.json file of the package and set metadata 
     * for further processing
     *
     * @return void
     */
    public function processPackage(): void
    {
        try {
            $json_file = @file_get_contents($this->path . '/composer.json');

            if (!$json_file) throw new Exception();

            $data = json_decode($json_file, true);
            $this->meta = $data;
            $this->name = $data['name'];
            $this->type = $data['type'];
            $this->extra = $data['extra'];
        } catch (Exception $e) {
            // To do: report and handle error
            throw new PackageException('Invalid Package');
        }
    }
}
