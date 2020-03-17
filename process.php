<?php
    require_once('vendor/autoload.php');
    use Scriptotek\Sru\Client as SruClient;

    try {
        $sru = new SruClient('http://catalogue.bnf.fr/api/SRU', [
            'schema' => 'unimarcXchange',
            'version' => '1.2'
        ]);
    } catch (ErrorException $e) {
        print "ERREUR ! ";
        exit;
    }

    # https://github.com/hackathonBnF/hackathon2016/wiki/API-Couverture-Service


$records = $sru->all('bib.fuzzyIsbn adj "2413003967"');
    # $records = $sru->all('alma.title="Hello world"');
    foreach ($records as $record) {
        $ark = $record->data->xpath('./child::*')[0]->attr("id");
        $url = sprintf("https://catalogue.bnf.fr/couverture?&appName=NE&idArk=%s&couverture=1", ($ark));
        $coverBnf = imagecreatefromjpeg($url);
        $x = imagesx($coverBnf );
        $y = imagesy($coverBnf );

        $im = imagecreatetruecolor($x, $y + 200);
        imagecopymerge($im, $coverBnf, 0, 0, 0, 0, $x, $y, 100);

        $copyright = imagecreatetruecolor($x, 200);
        $white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
        $black = imagecolorallocate($im, 0x00, 0x00, 0x00);
        $font_file = './arial.ttf';

        imagefilledrectangle($copyright, 0, 0, $x, 200, $white);
        imagefttext($copyright, 13, 0, 105, 55, $black, $font_file, 'Source : BnF');
        imagecopymerge($im, $copyright, 0, $y, 0, 0, $x, $y, 100);

        imagejpeg($im, "sortie.jpg");
        exit;
    }