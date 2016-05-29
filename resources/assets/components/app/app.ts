///<reference path="../../../../typings/index.d.ts"/>
import {Component} from '@angular/core';
import {ExpandableSearchComponent} from '../expandable-search/expandable-search';
import {TabsComponent} from '../tabs/tabs';
import {Tab} from '../../interfaces/tab';
import {TabListService} from '../../services/tab-list.service';
import {TabContentService} from '../../services/tab-content.service';

@Component({
    selector: 'web-chess',
    templateUrl: 'assets/components/app/app.html',
    directives: [
        ExpandableSearchComponent,
        TabsComponent
    ],
    providers: [
        TabListService,
        TabContentService
    ]
})
export class AppComponent
{
}
