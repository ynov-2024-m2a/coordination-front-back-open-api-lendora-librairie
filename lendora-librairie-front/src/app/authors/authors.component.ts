import { Component, OnInit } from '@angular/core';
import {MatListModule} from '@angular/material/list';
import { AuthorService, Author } from '../services/author.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-authors',
  standalone: true,
  imports: [MatListModule, CommonModule],
  templateUrl: './authors.component.html',
  styleUrl: './authors.component.scss'
})

export class AuthorsComponent implements OnInit {
  authors: Author[] = [];

  constructor(private authorService: AuthorService) {}

  ngOnInit(): void {
    this.authorService.getAuthors().subscribe({
      next: (data) => {this.authors = data; console.log(data)},
      error: (error) => console.error('Erreur lors de la récupération des livres:', error)
    })
  }
}
