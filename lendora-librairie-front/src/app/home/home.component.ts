import { Component } from '@angular/core';
import { BooksComponent } from '../books/books.component';
@Component({
  selector: 'app-home',
  standalone: true,
  imports: [BooksComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {

}
