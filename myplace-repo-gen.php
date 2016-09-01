<?php

$pluginroot = 'https://svn.strath.ac.uk/repos/moodle/plugins';
$packagesfile = __DIR__ . '/packages.json';

$repo = new \stdClass();
$repo->packages = [];

$plugins = svn_ls($pluginroot);

foreach ($plugins as $plugin) {

    $pluginname = strip_trailing_slash($plugin);

    $trees = svn_ls($pluginroot . '/' . $plugin);

    if (!in_array('tags/', $trees)) {
        echo "Tags not found for $pluginname\n";
        continue;
    }

    if (!in_array('branches/', $trees)) {
        echo "Branches not found for $pluginname\n";
        $branches = [];
    } else {
        $branches = svn_ls($pluginroot . '/' . $plugin . 'branches');
    }
    $tags = svn_ls($pluginroot . '/' . $plugin . 'tags');


    $package = new stdClass();

    $package->name = 'myplace-plugin/' . $pluginname;
    $package->description = $pluginname;
    $packageversions = [];
    foreach ($tags as $version) {
        $versionpackage = clone($package);
        $versionpackage->version = parse_version(strip_trailing_slash($version));
        if (is_null($versionpackage->version)) {
            // Maybe something like EARWIG
            continue;
        }
        // $versionpackage->type = 'moodle-' . $plugintype;
        $source = new \stdClass();
        $source->url = $pluginroot . '/' . $plugin;
        $source->type = 'svn';
        $source->reference =  'tags/' . $version;
        $versionpackage->source = $source;
        // $versionpackage->require = ['composer/installers' => '*'];
        /* if ($addcorerequires) {
            $supportedmoodles = [];
            foreach ($version->supportedmoodles as $supportedmoodle) {
                $supportedmoodles[] = $supportedmoodle->release . '.*';
            }
            $versionpackage->require['moodle/moodle'] = implode('||', $supportedmoodles);
        }
        $versionpackage->extra = ['installer-name' => $pluginname]; */
        $packageversions[$version] = $versionpackage;
    }

    foreach ($branches as $version) {
        $versionpackage = clone($package);
        $versionpackage->version = 'dev-' . (strip_trailing_slash($version));
        if (is_null($versionpackage->version)) {
            // Maybe something like EARWIG
            continue;
        }
        // $versionpackage->type = 'moodle-' . $plugintype;
        $source = new \stdClass();
        $source->url = $pluginroot . '/' . $plugin;
        $source->type = 'svn';
        $source->reference =  'branches/' . $version;
        $versionpackage->source = $source;
        $packageversions[$version] = $versionpackage;
    }

    $repo->packages[$package->name] = $packageversions;

}

file_put_contents($packagesfile, json_encode($repo));

function svn_ls($dir) {
    $list = `svn ls $dir`;
    return array_filter(explode("\n", $list), function($val) {
        return !empty($val) && strpos($val, '_') !== 0;
    });
}

function strip_trailing_slash($val) {
    return rtrim($val,"/");
}

function parse_version($versionstring) {
    $version = ltrim($versionstring, 'v');
    if (is_numeric($version)) {
        return $version;
    } else {
        return null;
    }
}
