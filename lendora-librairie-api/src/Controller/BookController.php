<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Genre;
use App\Repository\BookRepository;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
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
     * Return authors
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
        $jsonBooks = $serializer->serialize($books, 'json',["groups" => "getAllBooks"]);
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
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/books', name: 'book.post', methods: ['POST'])]
    public function createBook(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        // Désérialiser les données JSON dans un objet Book
        $data = json_decode($request->getContent(), true);

        $book = new Book();
        $book->setTitle($data['title'] ?? null);
        $book->setReleaseDate(new \DateTime($data['releaseDate'] ?? 'now'));
        $book->setBlurb($data['blurb'] ?? null);

        if (isset($data['authorId'])) {
            $author = $entityManager->getRepository(Author::class)->find($data['authorId']);
            if (!$author) {
                return new JsonResponse(['message' => 'Author not found'], JsonResponse::HTTP_NOT_FOUND);
            }
            $book->setAuthor($author);
        }

        // Récupérer les genres par leurs IDs et les associer
        if (isset($data['genreIds']) && is_array($data['genreIds'])) {
            foreach ($data['genreIds'] as $genreId) {
                $genre = $entityManager->getRepository(Genre::class)->find($genreId);
                if ($genre) {
                    $book->addGenre($genre);
                }
            }
        }

        // Valider les données du livre
        $errors = $validator->validate($book);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($book);
        $entityManager->flush();

        $location = $urlGenerator->generate('book.get', ['idBook' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse(
            $serializer->serialize($book, 'json', ['groups' => ['getAllBooks']]),
            JsonResponse::HTTP_CREATED,
            ['Location' => $location],
            true
        );
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
    #[Route('/api/books/{id}', name: 'book.update', methods: ['PUT'])]
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
    #[Route('/api/books/{id}', name: 'book.delete', methods: ['DELETE'])]
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
