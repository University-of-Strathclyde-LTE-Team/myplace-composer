<?php
$composerfile = 'composer.json';
$moodlepluginname = "moodle/moodle";
$vendorpath = __DIR__."/vendor";

echo "Vendor path: $vendorpath\n";

$composer = json_decode(file_get_contents($composerfile));

$deps = $composer->require;

$moodle = $deps->$moodlepluginname;

unset($deps->$moodlepluginname);

$actions = [];
foreach($deps as $name=>$info) {
    $pinfo = explode("/",$name);
    $pluginname = $pinfo[1];
    echo "$pluginname\n";
    $pluginpathinfo = explode("-",$pluginname);
    $pluginpathinfo = fix_known_path_issues($pluginpathinfo);
    $pluginpath = implode(DIRECTORY_SEPARATOR,$pluginpathinfo);
    
    $sourcepath = "{$vendorpath}/{$name}";
    echo "Install into {$pluginpath}\n";
    $a = array("{$sourcepath}", "{$vendorpath}/{$moodlepluginname}/{$pluginpath}");
    $actions[] = $a;
    
}

$remove_existing = true;
$dryrun = false;
foreach($actions as $a) {


        if (!file_exists($a[0])) {
            echo "Source file not found!";
        } else {
            if (file_exists($a[1]) && $remove_existing) {
                echo "Removing '{$a[1]}'\n";
                if (! $dryrun) { unlink($a[1]); }
            }
            echo "Symlink('{$a[0]}', '{$a[1]}')\n";
            if (! $dryrun) {symlink($a[0], $a[1]);}
    }
}

function fix_known_path_issues($pathinfo) {
    if ($pathinfo[0] == 'block') {
        $pathinfo[0] = 'blocks';
    }
    return $pathinfo;
}
