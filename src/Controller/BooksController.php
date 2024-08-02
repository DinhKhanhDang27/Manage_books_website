<?php

namespace App\Controller;

use App\Entity\Books;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BooksController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BooksRepository $booksRepository;

    public function __construct(EntityManagerInterface $entityManager, BooksRepository $booksRepository)
    {
        $this->entityManager = $entityManager;
        $this->booksRepository = $booksRepository;
    }

    #[Route('/books', name: 'books_list')]
    public function index(): Response
    {
        // Check if user has ROLE_ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('YOU ARE NOT ADMIN');
        }

        $books = $this->booksRepository->findAll();

        return $this->render('books/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/new', name: 'new_book')]
    public function new(): Response
    {
        return $this->render('books/new.html.twig');
    }

    #[Route('/books/create', name: 'create_book', methods: ['POST'])]
    public function create(Request $request): RedirectResponse
    {
        $book = new Books();

        // Xác nhận dữ liệu
        $bookId = $request->request->get('book_id');
        $title = $request->request->get('title');
        $authorId = (int) $request->request->get('author_id');
        $categoryId = (int) $request->request->get('category_id');
        $price = $request->request->get('price');
        $createdAt = \DateTime::createFromFormat('Y-m-d\TH:i', $request->request->get('created_at'));
        $updatedAt = \DateTime::createFromFormat('Y-m-d\TH:i', $request->request->get('updated_at'));

        // Kiểm tra và gán giá trị
        if ($createdAt === false || $updatedAt === false) {
            throw new \Exception('Invalid date format');
        }

        $book->setBookId($bookId);
        $book->setTitle($title);
        $book->setAuthorId($authorId);
        $book->setCategoryId($categoryId);
        $book->setPrice($price);
        $book->setCreatedAt($createdAt);
        $book->setUpdatedAt($updatedAt);

        // Persist và flush
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->redirectToRoute('books_list');
    }

    #[Route('/books/update/{id}', name: 'update_book')]
    public function update(int $id): Response
    {
        $book = $this->booksRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        return $this->render('books/update.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/books/edit/{id}', name: 'edit_book', methods: ['POST'])]
    public function edit(Request $request, int $id): RedirectResponse
    {
        $book = $this->booksRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $book->setBookId($request->request->get('book_id'));
        $book->setTitle($request->request->get('title'));
        $book->setAuthorId((int) $request->request->get('author_id'));
        $book->setCategoryId((int) $request->request->get('category_id'));
        $book->setPrice($request->request->get('price'));
        $book->setCreatedAt(new \DateTime($request->request->get('created_at')));
        $book->setUpdatedAt(new \DateTime($request->request->get('updated_at')));

        $this->entityManager->flush();

        return $this->redirectToRoute('books_list');
    }

    #[Route('/books/delete/{id}', name: 'delete_book', methods: ['GET'])]
    public function delete(int $id): RedirectResponse
    {
        $book = $this->booksRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->redirectToRoute('books_list');
    }

    #[Route('/viewbooks', name: 'view_books')]
    public function viewBooks(): Response
    {
        $books = $this->booksRepository->findAll();

        return $this->render('books/viewbooks.html.twig', [
            'books' => $books,
        ]);
    }
}
