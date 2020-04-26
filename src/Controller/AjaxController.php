<?php

namespace App\Controller;

use App\Entity\Cover;
use App\Entity\Isbn;
use App\Repository\IsbnRepository;
use App\Service\BnfClient;
use App\Service\IsbnLibrary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/api/v1/{isbn}/{size}/{force?}", name="ajax_single")
     */
    public function index(EntityManagerInterface $em, IsbnRepository $isbnRepository, BnfClient $bnfClient, string $isbn = null, string $force = null, string $size)
    {
        $isbnObject = $isbnRepository->findByValue($isbn);
        if (!is_null($force)) {
            $isbnObject->setCover(null);
        }

        if (is_null($isbnObject)) {
            // On va créer un isbnObject null
            $isbnObject = new Isbn($isbn);
            $em->persist($isbnObject);
            $em->flush();
        }


        if (!$isbnObject->hasCoverPathForSize($size)) {
            $cover = $bnfClient->getCoverForIsbn($isbnObject, $size, $em);
            $isbnObject->setCover($cover);
            $em->persist($isbnObject);
            $em->flush();
        }

        // À partir de là on a une couverture, on va la renvoyer
        return new BinaryFileResponse($isbnObject->getCover()->getFilepath($size));
    }
}
