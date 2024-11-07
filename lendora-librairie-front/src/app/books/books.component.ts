import { Component, OnInit } from '@angular/core';
import {MatListModule} from '@angular/material/list';
import { BookService, Book } from '../services/book.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-books',
  standalone: true,
  imports: [MatListModule, CommonModule],
  templateUrl: './books.component.html',
  styleUrl: './books.component.scss'
})

export class BooksComponent implements OnInit {
  books: Book[] = [];

  constructor(private bookService: BookService) {}

  ngOnInit(): void {
    this.bookService.getBooks().subscribe({
      next: (data) => {this.books = data; console.log(data)},
      error: (error) => console.error('Erreur lors de la récupération des livres:', error)
    })
  }
}
