import { Component, OnInit, ChangeDetectionStrategy, inject, OnDestroy } from '@angular/core';
import {MatListModule} from '@angular/material/list';
import { AuthorService, Author } from '../services/author.service';
import { CommonModule } from '@angular/common';
import {MatCard, MatCardActions, MatCardHeader, MatCardSubtitle, MatCardTitle} from "@angular/material/card";
import {MatButtonModule} from "@angular/material/button";
import {MatDialog, MatDialogModule} from '@angular/material/dialog';
import {AuthorUpdateDialogComponent} from "../author-update-dialog/author-update-dialog.component";
import {AuthorFormComponent} from "../author-form/author-form.component";
import {SharedService} from "../services/shared.service";
import { Subscription } from 'rxjs';
import {MatIcon} from "@angular/material/icon";

@Component({
  selector: 'app-authors',
  standalone: true,
  imports: [MatListModule, CommonModule, MatCard, MatCardHeader, MatCardTitle, MatCardSubtitle, MatCardActions, MatButtonModule, MatDialogModule, MatIcon],

  templateUrl: './authors.component.html',
  styleUrl: './authors.component.scss'
})

export class AuthorsComponent implements OnInit, OnDestroy {
  authors: Author[] = [];
  private formSubmitSubscription!: Subscription;
  constructor(private authorService: AuthorService, private sharedService: SharedService) {}

  ngOnInit(): void {
    this.authorService.getAuthors().subscribe({
      next: (data) => {this.authors = data; console.log(data); console.log(this.authors);},
      error: (error) => console.error('Erreur lors de la récupération des auteurs:', error)
    });
    this.formSubmitSubscription = this.sharedService.formSubmit$.subscribe(() => {
      this.refreshContent();
    });
  }

  refreshContent(): void {
    this.ngOnInit();
  }

  ngOnDestroy(): void {
    // Se désabonner pour éviter les fuites de mémoire
    this.formSubmitSubscription.unsubscribe();
  }

  readonly dialog = inject(MatDialog);
  openDialog(author: Author) {
    const dialogRef = this.dialog.open(AuthorUpdateDialogComponent, {
      data: author
    });

    dialogRef.afterClosed().subscribe(result => {

      this.ngOnInit();
    });
  }

  deleteAuthor(author: Author){
    if(confirm("Êtes vous sûr de vouloir supprimer l'auteur : "+author.name+" "+author.lastName+" ?")) {
      this.authorService.deleteAuthor(author.id).subscribe({
        next: (data) => {this.ngOnInit();},
        error: (error) => console.error('Erreur lors de la suppression de l\'auteur:', error)
      });
    }

  }

}
