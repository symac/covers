<?php


namespace App\Service;


use App\Entity\Cover;
use Scriptotek\Sru\Client as SruClient;

class BnfClient
{

    private $isbnLibrary;

    public function __construct(IsbnLibrary $isbnLibrary)
    {
        $this->isbnLibrary = $isbnLibrary;
    }

    public function getRecordsForIsbn($isbn)
    {
        $isbn = $this->isbnLibrary->clean($isbn);

        try {
            $sru = new SruClient('http://catalogue.bnf.fr/api/SRU', [
                'schema' => 'unimarcXchange',
                'version' => '1.2'
            ]);
        } catch
        (ErrorException $e) {
            print "ERREUR ! ";
            exit;
        }

        $records = $sru->all('bib.fuzzyIsbn adj "' . $isbn . '"');

        return $records;
    }

    public function getCoverFromArk($ark)
    {
        $cover = new Cover();

        try {
            $url = sprintf("https://catalogue.bnf.fr/couverture?&appName=NE&idArk=%s&couverture=1", ($ark));
            $cover->setUrl($url);

            $coverBnf = imagecreatefromjpeg($url);
            $x = imagesx($coverBnf);
            $y = imagesy($coverBnf);

            $cover->setWidth($x);
            $cover->setHeight($y);

            $im = imagecreatetruecolor($x, $y + 200);
            imagecopymerge($im, $coverBnf, 0, 0, 0, 0, $x, $y, 100);

            $copyright = imagecreatetruecolor($x, 200);
            $white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
            $black = imagecolorallocate($im, 0x00, 0x00, 0x00);
            $font_file = __DIR__ . '/../../arial.ttf';

            imagefilledrectangle($copyright, 0, 0, $x, 200, $white);
            imagefttext($copyright, 13, 0, 105, 55, $black, $font_file, 'Source : BnF_');
            imagecopymerge($im, $copyright, 0, $y, 0, 0, $x, $y, 100);

            $filename = md5($ark) . ".jpg";
            $cover->setFilename($filename);
            imagejpeg($im, __DIR__ . "/../../public/cover/" . $cover->getFilename());
            return $cover;
        } catch (\Exception $e) {
            return null;
        }
    }
}