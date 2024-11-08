import {ChangeDetectionStrategy, Component, inject, OnInit} from '@angular/core';
import {MatButtonModule} from "@angular/material/button";
import {MAT_DIALOG_DATA, MatDialogModule, MatDialogRef} from "@angular/material/dialog";
import {FormBuilder, FormGroup, ReactiveFormsModule, Validators} from '@angular/forms';
import {MatError, MatFormField, MatLabel} from "@angular/material/form-field";
import {MatInput} from "@angular/material/input";
import {MatDatepicker, MatDatepickerInput, MatDatepickerToggle} from "@angular/material/datepicker";
import {Author, AuthorService} from "../services/author.service";
import { CommonModule } from '@angular/common';
import {Router} from "@angular/router";
import {HttpClientModule} from "@angular/common/http";
@Component({
  selector: 'app-author-update-dialog',
  standalone: true,
  imports: [MatDialogModule, MatButtonModule, MatLabel, MatFormField, MatInput, ReactiveFormsModule, MatDatepicker, MatDatepickerToggle, MatDatepickerInput, MatError, CommonModule, HttpClientModule],
  changeDetection: ChangeDetectionStrategy.OnPush,
  templateUrl: './author-update-dialog.component.html',
  styleUrl: './author-update-dialog.component.scss'
})
export class AuthorUpdateDialogComponent implements OnInit {
  authorForm!: FormGroup;
  readonly dialogRef = inject(MatDialogRef<AuthorUpdateDialogComponent>);
  readonly author = inject<Author>(MAT_DIALOG_DATA);
  constructor(private fb: FormBuilder, private authorService: AuthorService, private router: Router) {}

  ngOnInit(): void {
    // Initialisation du formulaire avec les champs requis et les validations
    this.authorForm = this.fb.group({
      name: [this.author.name, Validators.required],
      lastname: [this.author.lastName, Validators.required],
      birthday: [this.author.birthday, Validators.required],
      biography: [this.author.biography]  // facultatif, donc pas de Validators.required
    });
  }

  onSubmit(): void {
    if (this.authorForm.valid) {
      const updatedAuthor: Author = this.authorForm.value;
      if(updatedAuthor  != this.author){
        console.log('Données du formulaire auteur:', this.authorForm.value);

        this.authorService.updateAuthor(updatedAuthor, this.author.id).subscribe({
          next: (response) => {
            console.log('Auteur mis à jour avec succès', response);



            this.router.navigateByUrl('/', { skipLocationChange: true }).then(() => {
              this.router.navigate([this.router.url]).then(r => {
                this.dialogRef.close();
              });
            });
          },
          error: (error) => {
            console.error('Erreur lors de l\'ajout de l\'auteur, error');
          }
        });
      }

    } else {
      console.log('Le formulaire est invalide');
    }
  }
}
