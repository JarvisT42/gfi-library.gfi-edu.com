<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($_SESSION['book_bag'])) {
    $_SESSION['book_bag'] = array_filter($_SESSION['book_bag'], function($book) use ($data) {
        return $book['title'] !== $data['title'] ||
               $book['author'] !== $data['author'] ||
               $book['publicationDate'] !== $data['publicationDate'] ||
               $book['table'] !== $data['table'] ||
               $book['coverImage'] !== $data['coverImage'] ||
               $book['copies'] !== $data['copies'];
    });
}

echo json_encode(['status' => 'success']);
?>
