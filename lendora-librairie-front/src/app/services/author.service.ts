import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Author {
  id: number;
  name: string;
  lastName: string;
  birthday: Date;
  biography: string;
}

@Injectable({
  providedIn: 'root'
})

export class AuthorService {

  private apiUrl = 'http://localhost:8000/api/authors';

  constructor(private http: HttpClient) {}

  getAuthors(): Observable<Author[]> {
    return this.http.get<Author[]>(this.apiUrl);
  }

  getAuthor(id: number): Observable<Author[]> {
    return this.http.get<Author[]>(this.apiUrl+"/"+id);
  }

  addAuthor(author: Author): Observable<Author> {
    return this.http.post<Author>(this.apiUrl, author);
  }

  updateAuthor(author: Author, id: number): Observable<Author> {
    return this.http.put<Author>(this.apiUrl+"/"+id, author);
  }

  deleteAuthor(id: number): Observable<Author> {
    return this.http.delete<Author>(this.apiUrl+"/"+id);
  }
}
