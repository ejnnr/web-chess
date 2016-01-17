import {Component} from 'angular2/core';
import {ExpandableSearchComponent} from '../expandable-search/expandable-search';
import {TabsComponent} from '../tabs/tabs';
import {Tab} from '../../interfaces/tab';

@Component({
    selector: 'web-chess',
    templateUrl: 'js/components/app/app.html',
    directives: [
        ExpandableSearchComponent,
        TabsComponent
    ]
})
export class AppComponent
{
    tabList: Tab[] = [
        {
            "name": "Tab 1",
            "content": "<h1>Hello</h1>"
        },
        {
            "name": "Tab 2",
            "content": "<h1>World</h1>"
        },
        {
            "name": "Tab 3",
            "content": "<h1>!</h1>"
        }
    ];
}
