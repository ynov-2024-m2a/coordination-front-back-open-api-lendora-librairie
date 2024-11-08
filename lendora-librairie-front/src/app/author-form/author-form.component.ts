import {Component, OnInit} from '@angular/core';
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
import {Router} from "@angular/router";
import {SharedService} from "../services/shared.service";

@Component({
  selector: 'app-author-form',
  standalone: true,
  imports: [ReactiveFormsModule, MatFormFieldModule, MatInputModule, MatError, MatDatepickerInput,
    MatDatepickerModule, MatButtonModule, MatLabel, CommonModule, MatDatepickerModule, MatNativeDateModule, MatIcon, MatHint,
    HttpClientModule],
  templateUrl: './author-form.component.html',
  styleUrl: './author-form.component.scss'
})
export class AuthorFormComponent  implements OnInit {
  authorForm: FormGroup;

  constructor(private fb: FormBuilder, private authorService: AuthorService, private router: Router, private sharedService: SharedService) {
    this.authorForm = this.fb.group({
      name: ['', Validators.required],
      lastName: ['', Validators.required],
      birthday: [null, Validators.required],
      biography: ['']
    });
  }

  ngOnInit(): void {
    this.authorForm = this.fb.group({
      name: ['', ],
      lastName: ['', ],
      birthday: [null, ],
      biography: ['']
    });

  }

  // Soumettre le formulaire
  onSubmit(): void {
    if (this.authorForm.valid) {

      const newAuthor: Author = this.authorForm.value;
      this.authorService.addAuthor(newAuthor).subscribe({
        next: (response) => {
         this.router.navigateByUrl('/', { skipLocationChange: true }).then(() => {
            this.router.navigate([this.router.url]).then(r => {
              this.sharedService.notifyFormSubmit();
              this.ngOnInit();
              this.authorForm.reset();
              this.authorForm.clearValidators();

            });
          });
        },
        error: (error) => {
          console.error('Erreur lors de l\'ajout de l\'auteur, error');
        }
      });
    } else {

      console.log('Formulaire invalide');
    }
  }
}
