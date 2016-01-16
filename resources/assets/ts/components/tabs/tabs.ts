import {Component, Input} from 'angular2/core';
import {Tab} from '../../interfaces/tab';

@Component({
    selector: 'tabs',
    templateUrl: 'js/components/tabs/tabs.html',
    styleUrls: ['js/components/tabs/tabs.css']
})
export class TabsComponent
{
    @Input()
    tabs: Tab[];

    closeTab(tab: Tab) {
        var index: number = this.tabs.indexOf(tab);
        if (index > -1) {
            delete this.tabs[index];
        }
    }
}
