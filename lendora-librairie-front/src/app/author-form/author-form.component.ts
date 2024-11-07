import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthorService, Author } from '../services/author.service';
import { ReactiveFormsModule } from '@angular/forms';
import {MatError, MatFormField, MatFormFieldModule, MatHint} from "@angular/material/form-field";
import {MatInput, MatInputModule} from "@angular/material/input";
import {MatDatepicker, MatDatepickerInput, MatDatepickerModule} from "@angular/material/datepicker";
import {MatButton, MatButtonModule} from "@angular/material/button";
import {MatLabel} from "@angular/material/form-field";
import { CommonModule } from '@angular/common';
import {MatNativeDateModule} from "@angular/material/core";
import {MatIcon} from "@angular/material/icon";
import {HttpClientModule} from "@angular/common/http";

@Component({
  selector: 'app-author-form',
  standalone: true,
  imports: [ReactiveFormsModule, MatFormFieldModule, MatInputModule, MatError, MatDatepickerInput,
    MatDatepickerModule, MatButtonModule, MatLabel, CommonModule, MatDatepickerModule, MatNativeDateModule, MatIcon, MatHint,
    HttpClientModule],
  templateUrl: './author-form.component.html',
  styleUrl: './author-form.component.scss'
})
export class AuthorFormComponent {
  authorForm: FormGroup;

  constructor(private fb: FormBuilder, private authorService: AuthorService) {
    this.authorForm = this.fb.group({
      name: ['', Validators.required],
      lastName: ['', Validators.required],
      birthday: [null, Validators.required],
      biography: ['']
    });
  }

  // Soumettre le formulaire
  onSubmit(): void {
    if (this.authorForm.valid) {
      const newAuthor: Author = this.authorForm.value;
      this.authorService.addAuthor(newAuthor).subscribe({
        next: (response) => {
          console.log('Auteur ajouté avec succès', response);
          this.authorForm.reset(); // Réinitialiser le formulaire après l'ajout
        },
        error: (error) => {
          console.log(newAuthor);
          console.error('Erreur lors de l\'ajout de l\'auteur, error');
        }
      });
    } else {

      console.log('Formulaire invalide');
    }
  }
}
