import { NgModule }			from '@angular/core';
import { BrowserModule }	from '@angular/platform-browser';
import { FormsModule }		from '@angular/forms';
import { HttpModule }		from '@angular/http';
import { InMemoryWebApiModule } from 'angular-in-memory-web-api';

import { ScoreReporterModule }	from './score-reporter/score-reporter.module';
import { AppRoutingModule }     from './app-routing.module';
import { LoginRoutingModule }      from './login-routing.module';

import { AppComponent }			from './app.component';
import { DashboardComponent }			from './dashboard.component';
import { HeroDetailComponent }	from './hero-detail.component';
import { HeroesComponent }		from './heroes.component';
import { PageNotFoundComponent }	from './not-found.component';
import { HeroSearchComponent }	from './hero-search.component';
import { LoginComponent }          from './login.component';

import { InMemoryDataService }  from './in-memory-data.service';
import { HeroService }			from './hero.service';
import { DialogService }           from './dialog.service';

@NgModule({
	imports:      [ 
		BrowserModule,
		FormsModule,
		HttpModule,
		InMemoryWebApiModule.forRoot(InMemoryDataService),
		LoginRoutingModule,
		ScoreReporterModule,
		AppRoutingModule
	],
	declarations: [
		AppComponent,
		HeroDetailComponent,
		HeroesComponent,
		DashboardComponent,
		HeroSearchComponent,
		LoginComponent,
		PageNotFoundComponent
	],
	providers: [
		HeroService,
		DialogService
	],
	bootstrap: [ 
		AppComponent 
	]
})

export class AppModule { }
