<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * Return books
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
        * Return book with id
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

   /**
     * Create new book
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/book', name: 'book.post', methods: ['POST'])]
    public function createBook(Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse{
        $book = $serializer->deserialize($request->getContent(), Book::class,'json');  
        
        $errors = $validator->validate($book);
        if($errors ->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        
        $entityManager->persist($book);
        $entityManager->flush();
        $cache->invalidateTags(["bookCache"]);

        $jsonBook= $serializer->serialize($book,'json');

        $location = $urlGenerator->generate('book.get', ['idBook'=> $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook,Response::HTTP_CREATED,["Location" => $location],true);

    }

    /** 
     * Update book with a id
     *
     * @param Book $book
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/book/{id}', name: 'book.update', methods: ['PUT'])]
    public function updateBook(Book $book, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        $updatedBook = $serializer->deserialize($request->getContent(), Book::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>$book]);
        $entityManager->persist($updatedBook);
        $entityManager->flush();
        $cache->invalidateTags(["bookCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }

    /** 
     * Delete book with a id
     *
     * @param Book $books
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/book/{id}', name: 'book.delete', methods: ['DELETE'])]
    public function softDeleteBook(Book $books, Request $request, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{
        
        $book = $request->toArray()["force"];
        if($book === true){
            $entityManager->remove($books);
            
        }

        $entityManager->flush();
        $cache->invalidateTags(["bookCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }
}
