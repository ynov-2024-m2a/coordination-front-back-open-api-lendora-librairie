<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }

    /**
     * Renvoie tous les livres
        *
        * @param BookRepository $repository
        * @param SerializerInterface $serializer
        * @return JsonResponse
     */
    #[Route('/api/books', name: 'book.getAll', methods:['GET'])]
    public function getAllBooks(
        BookRepository $repository,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $books =  $repository->findAll();
        $jsonBooks = $serializer->serialize($authors, 'json',["groups" => "getAllBooks"]);
        return new JsonResponse(    
            $jsonBooks,
            Response::HTTP_OK, 
            [], 
            true
        );
    } 

    /**
        * Renvoie un book par son id
        *
        * @param Book $book
        * @param SerializerInterface $serializer
        * @return JsonResponse
    */
    #[Route('/api/books/{idBook}', name:  'book.get', methods: ['GET'])]
    #[ParamConverter("book", options: ["id" => "idBook"])]
    
   public function getBook(Book $book, SerializerInterface $serializer): JsonResponse 
   {
       $jsonBooks = $serializer->serialize($book, 'json', ["groups" => "getAllBooks"]);
       return new JsonResponse($jsonBooks, Response::HTTP_OK, ['accept' => 'json'], true);
   }
}
