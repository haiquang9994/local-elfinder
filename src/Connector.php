<?php

namespace Lpks\LocalElfinder;

use elFinder;
use elFinderConnector;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

class Connector
{
    protected $options = [];
    protected $root_dir = null;
    protected $elfinder_dir = null;
    protected $debug = true;

    public function __construct(string $root_dir, string $elfinder_dir, bool $debug = true)
    {
        $this->root_dir = $root_dir;
        $this->elfinder_dir = $elfinder_dir;
        $this->debug = $debug;
    }

    public function addOption(string $name, array $option)
    {
        $this->options[$name] = [
            'uploadAllow' => (isset($option['uploadAllow']) && is_array($option['uploadAllow'])) ? $option['uploadAllow'] : ['image'],
            'uploadDeny' => (isset($option['uploadDeny']) && is_array($option['uploadDeny'])) ? $option['uploadDeny'] : [],
            'uploadOrder' => (isset($option['uploadOrder']) && is_array($option['uploadOrder'])) ? $option['uploadOrder'] : ['allow', 'deny'],
            'disabled' => (isset($option['disabled']) && is_array($option['disabled'])) ? $option['disabled'] : ['mkfile', 'rm', 'archive', 'extract'],
        ];
    }

    protected function finderAccess($attr, $path, $data, $volume)
    {
        return (strpos(basename($path), '.') === 0) ? !($attr == 'read' || $attr == 'write') : null;
    }

    protected function getOpts()
    {
        $root_name = substr($this->root_dir, strrpos($this->root_dir, '/') + 1);
        if (strlen($root_name) > 0) {
            $root_dir = $this->root_dir;
            $roots = [];
            foreach ($this->options as $name => $option) {
                $roots[] = [
                    'alias' => $name,
                    'imgLib' => 'auto',
                    'driver' => 'Flysystem',
                    'filesystem' => new Filesystem(new LocalAdapter($root_dir . DIRECTORY_SEPARATOR . $name)),
                    'path' => '/',
                    'tmbPath' => $root_name . '/.tmb',
                    'URL' => '/' . $root_name . '/' . $name,
                    'tmbURL' => '/' . $root_name . '/.tmb',
                    'tmbBgColor' => 'transparent',
                    'tmbCrop' => true,
                    'tmbSize' => 40,
                    'accessControl' => [$this, 'finderAccess'],
                    'uploadAllow' => $option['uploadAllow'] ?? [],
                    'uploadDeny' => $option['uploadDeny'] ?? [],
                    'uploadOrder' => $option['uploadOrder'] ?? [],
                    'disabled' => $option['disabled'] ?? [],
                ];
            }

            $opts = [
                'locale' => 'en_US.UTF-8',
                'debug' => $this->debug,
                'roots' => $roots,
            ];

            return $opts;
        }
        return null;
    }

    protected function allowAccess()
    {
        return true;
    }

    public function run()
    {
        if (!defined('ELFINDER_IMG_PARENT_URL')) {
            define('ELFINDER_IMG_PARENT_URL', $this->elfinder_dir);
        }

        if (!$this->allowAccess()) {
            exit;
        }

        $connector = new elFinderConnector(new elFinder($this->getOpts()));
        $connector->run();
    }
}
