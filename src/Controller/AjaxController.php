<?php

namespace App\Controller;

use App\Repository\IsbnRepository;
use App\Service\BnfClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax/single/{isbn}", name="ajax")
     */
    public function index(IsbnRepository $isbnRepository, BnfClient $bnfClient, string $isbn)
    {
        $isbnObject = $isbnRepository->findByValue($isbn);
        if (is_null($isbnObject)) {
            // On va aller interroger le serveur de la BnF
            $records = $bnfClient->getRecordsForIsbn($isbn);
            foreach ($records as $record) {
                $ark = $record->data->xpath('./child::*')[0]->attr("id");
                $bnfClient->getCoverFromArk($ark);
            }
        }

        return $this->render('ajax/index.html.twig', [
            'controller_name' => 'AjaxController',
        ]);
    }
}
