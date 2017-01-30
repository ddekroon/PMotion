/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


import { Component } from '@angular/core';
@Component({
  selector: 'my-app',
  template: `
    <h1>{{title}}</h1>
	<nav>
		<a routerLink="/dashboard" routerLinkActive="active">Dashboard</a>
		<a routerLink="/heroes" routerLinkActive="active">Heroes</a>
	</nav>
	<router-outlet></router-outlet>
  `
})
export class AppComponent {
  title = 'PMotion';
}