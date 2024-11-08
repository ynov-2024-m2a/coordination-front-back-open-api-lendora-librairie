import { Component, OnInit, ChangeDetectionStrategy, inject } from '@angular/core';
import {MatListModule} from '@angular/material/list';
import { AuthorService, Author } from '../services/author.service';
import { CommonModule } from '@angular/common';
import {MatCard, MatCardActions, MatCardHeader, MatCardSubtitle, MatCardTitle} from "@angular/material/card";
import {MatButtonModule} from "@angular/material/button";
import {MatDialog, MatDialogModule} from '@angular/material/dialog';
import {AuthorUpdateDialogComponent} from "../author-update-dialog/author-update-dialog.component";

@Component({
  selector: 'app-authors',
  standalone: true,
  imports: [MatListModule, CommonModule, MatCard, MatCardHeader, MatCardTitle, MatCardSubtitle, MatCardActions, MatButtonModule, MatDialogModule],

  templateUrl: './authors.component.html',
  styleUrl: './authors.component.scss'
})

export class AuthorsComponent implements OnInit {
  authors: Author[] = [];

  constructor(private authorService: AuthorService) {}

  ngOnInit(): void {
    this.authorService.getAuthors().subscribe({
      next: (data) => {this.authors = data; console.log(data); console.log(this.authors);},
      error: (error) => console.error('Erreur lors de la récupération des auteurs:', error)
    })
  }
  readonly dialog = inject(MatDialog);
  openDialog(author: Author) {
    const dialogRef = this.dialog.open(AuthorUpdateDialogComponent, {
      data: author
    });

    dialogRef.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }

}
