#!/usr/bin/php
<?php
// Writes a JSON file with all images in a Commons category
require 'dlcomcatcm.php';

function request($cmcontinue = false) {
    $args = array(
        "format" => "json",
        "action" => "query",
        "list" => "categorymembers",
        "cmtitle" => "Category:Images_from_Wiki_Loves_Monuments",
        "cmlimit" => "500"
    );

    if ($cmcontinue) {
        $args['cmcontinue'] = $cmcontinue;
    }
    
    $mw = new MwApiRequest($args);
    return $mw->getResponse();
}

$images = array();
$firstquery = true;

do {
    if (isset($data['query-continue'])) {
        $cmcontinue = $data['query-continue']['categorymembers']['cmcontinue'];
    } else {
        // First time or last query?
        if ($firstquery) {
            $cmcontinue = true;
            $firstquery = false;
        } else {
            echo "No more images..\n";
            break;
        }
    }

    $data = request($cmcontinue);

    if ($data['error']) {
        echo "FATAL ERROR: " . $data['error']['info'] . "\n";
        break;
    } else {
        echo "Adding images...\n";

        foreach ($data['query']['categorymembers'] as $c) {
            echo $c['title'] . "\n";
            $images[] = $c;
        }

        file_put_contents("files.json", json_encode($images));
    }
} while (true);

echo "Done!\n";
print_r($images);