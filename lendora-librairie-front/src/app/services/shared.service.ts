import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SharedService {

  constructor() { }

  private formSubmitSource = new Subject<void>();

  // Observable pour écouter les événements d'envoi du formulaire
  formSubmit$ = this.formSubmitSource.asObservable();

  // Méthode pour émettre un événement d'envoi de formulaire
  notifyFormSubmit(): void {
    this.formSubmitSource.next();
  }
}
