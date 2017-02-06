import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { DashboardComponent }   from './dashboard.component';
import { HeroesComponent }      from './heroes.component';
import { HeroDetailComponent }  from './hero-detail.component';
import { PageNotFoundComponent } from './not-found.component';

import { CanDeactivateGuard }       from './can-deactivate-guard.service';
import { AuthGuard }                from './auth-guard.service';
import { SelectivePreloadingStrategy } from './selective-preloading-strategy';

const appRoutes: Routes = [
	
	{ path: 'dashboard',  component: DashboardComponent },
	{ path: 'detail/:id', component: HeroDetailComponent },
	{ 
		path: 'heroes',
		component: HeroesComponent,
		data: { title: 'Heroes List' } 
	},
	{
		path: 'score-reporter',
		loadChildren: 'app/score-reporter/score-reporter.module#ScoreReporterModule',
		data: { preload: true }
	},
	{ path: '', redirectTo: '/dashboard', pathMatch: 'full' },
	{ path: '**', component: PageNotFoundComponent } //Good for a 404 later on
];

@NgModule({
	imports: [ 
		RouterModule.forRoot(
			appRoutes,
			{ preloadingStrategy: SelectivePreloadingStrategy }
		)
	],
	exports: [ RouterModule ],
	providers: [
		CanDeactivateGuard,
		SelectivePreloadingStrategy
	]
})
export class AppRoutingModule {}