import {Component, Input} from 'angular2/core';
import {TabContentComponent} from '../tab-content/tab-content.component';
import {Tab} from '../../interfaces/tab';
import {TabListService} from '../../services/tab-list.service';

@Component({
    selector: 'tabs',
    templateUrl: 'js/components/tabs/tabs.html',
    styleUrls: ['js/components/tabs/tabs.css'],
    directives: [
        TabContentComponent
    ]
})
export class TabsComponent
{
    private _counter = 0;

    constructor(private _tabListService: TabListService) {}

    closeTab(tab: Tab) {
        this._tabListService.removeTab(tab);
    }

    getTabs(): Tab[] {
        return this._tabListService.getTabs();
    }

    newTab() {
        this._tabListService.addTab({
            "name": "Tab " + ++this._counter,
            "layoutName": "game"
        });
    }
}
