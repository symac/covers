<?php


namespace App\Service;


use App\Entity\Cover;
use App\Entity\Isbn;
use Doctrine\ORM\EntityManagerInterface;
use Scriptotek\Sru\Client as SruClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BnfClient
{
    private $sizes = [
        "small" => [
            "copyrightHeight" => 36,
            "fontSize" => 12,
        ],
        "medium" => [
            "copyrightHeight" => 50,
            "fontSize" => 16,
        ],
        "large" => [
            "copyrightHeight" => 50,
            "fontSize" => 16,
        ]
    ];

    public function getArkFromIsbn(Isbn $isbn)
    {
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

        $records = $sru->all('bib.fuzzyIsbn adj "' . $isbn->getValue13() . '"');
        foreach ($records as $record) {
            $ark = $record->data->xpath('./child::*')[0]->attr("id");
            return $ark;
        }

        return null;
    }

    private function downloadOriginal(Cover $cover)
    {
        try {
            $client = HttpClient::create();
            $url = sprintf("https://catalogue.bnf.fr/couverture?&appName=NE&idArk=%s&couverture=1", ($cover->getArk()));
            $response = $client->request("GET", $url);

            if ((isset($response->getHeaders()["content-length"])) && (intval($response->getHeaders()["content-length"][0]) == 4566)) {
                return null;
            }

            file_put_contents($cover->getFilepath("original"), $response->getContent());

            $coverBnf = imagecreatefromjpeg($cover->getFilepath("original"));
            $cover->setWidth(imagesx($coverBnf));
            $cover->setHeight(imagesy($coverBnf));
            $cover->setUrl($url);

            return $cover;
        } catch (\Exception $e) {
            dd($e);
            return null;
        }
    }

    public function getCoverForIsbn(Isbn $isbnObject, $size)
    {
        // We need to get the cover
        if (is_null($isbnObject->getCover())) {
            $cover = new Cover();
            $ark = $this->getArkFromIsbn($isbnObject);
            $cover->setArk($ark);
            $cover = $this->downloadOriginal($cover);
        } else {
            $cover = $isbnObject->getCover();
        }

        $cover = $this->buildDerivative($cover, $size);
        return $cover;
    }

    public function buildDerivative(Cover $cover, $size)
    {
        $font_file = __DIR__ . '/../../Ubuntu-C.ttf';
        $copyrightText = "Source : BnF";
        try {
            $derivativeWidth = $cover->getDerivativeWidth($size);
            $derivativeHeight = $derivativeWidth * $cover->getHeight() / $cover->getWidth();
            $copyrightHeight = $this->sizes[$size]["copyrightHeight"];
            $font_size = $this->sizes[$size]["fontSize"];

            $im = imagecreatetruecolor($derivativeWidth, $derivativeHeight + $copyrightHeight);
            $white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
            $black = imagecolorallocate($im, 0x00, 0x00, 0x00);

            $coverBnf = imagecreatefromjpeg($cover->getFilepath("original"));
            imagecopyresampled($im, $coverBnf, 0, 0, 0, 0, $derivativeWidth, $derivativeHeight, $cover->getWidth(), $cover->getHeight());

            $copyrightImage = imagecreatetruecolor($derivativeWidth, $copyrightHeight);
            $copyrightBorderStrengh = 3;
            $copyrightBorderColor = imagecolorallocate($im, 0x00, 0x00, 0x00);
            $copyrightBackgroundColor = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

            $copyrightTextBox = imagettfbbox($font_size, null, $font_file, $copyrightText);
            $copyrightTextWidth = $copyrightTextBox[2] - $copyrightTextBox[0];
            $copyrightTextHeight = $copyrightTextBox[7] - $copyrightTextBox[1];

            $copyrightX = ($derivativeWidth / 2) - ($copyrightTextWidth / 2);
            $copyrightY = ($copyrightHeight / 2) - ($copyrightTextHeight / 2);

            imagefilledrectangle($copyrightImage, 0, 0, $derivativeWidth, $copyrightHeight, $copyrightBorderColor);
            imagefilledrectangle($copyrightImage, $copyrightBorderStrengh, $copyrightBorderStrengh, $derivativeWidth - $copyrightBorderStrengh, $copyrightHeight - $copyrightBorderStrengh, $copyrightBackgroundColor);
            imagettftext($copyrightImage, $font_size, 0, $copyrightX, $copyrightY, $black, $font_file, $copyrightText);
            imagecopymerge($im, $copyrightImage, 0, $derivativeHeight, 0, 0, $derivativeWidth, $copyrightHeight, 100);

            imagejpeg($im, $cover->getFilePath($size));

            $setFunction = "set".ucfirst($size);
            $cover->$setFunction(true);
            return $cover;
        } catch (\Exception $e) {
            dd($e);
            return null;
        }
    }
}