import {Component, Input} from 'angular2/core';
import {Tab} from '../../interfaces/tab';
import {TabListService} from '../../services/tab-list.service';
import {TabContentService} from '../../services/tab-content.service';

@Component({
    selector: 'tabs',
    templateUrl: 'js/components/tabs/tabs.html',
    styleUrls: ['js/components/tabs/tabs.css']
})
export class TabsComponent
{
    private _counter = 0;

    constructor(private _tabListService: TabListService, private _tabContentService: TabContentService) {}

    closeTab(tab: Tab) {
        this._tabListService.removeTab(tab);
    }

    getTabs(): Tab[] {
        return this._tabListService.getTabs();
    }

    newTab() {
        this._tabListService.addTab({
            "name": "Tab " + ++this._counter,
            "layout": "greeter"
        });
    }

    getContent(tab: Tab) {
        return this._tabContentService.getContent(tab);
    }
}
