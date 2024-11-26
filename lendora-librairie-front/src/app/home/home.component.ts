import { Component } from '@angular/core';
import { AuthorsComponent } from '../authors/authors.component';
import {AuthorFormComponent} from "../author-form/author-form.component";
import {MatDivider} from "@angular/material/divider";
@Component({
  selector: 'app-home',
  standalone: true,
  imports: [AuthorsComponent, AuthorFormComponent, MatDivider],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {

}
