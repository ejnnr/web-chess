///<reference path="../../../../typings/index.d.ts"/>
import {Component, Input, ViewChild} from '@angular/core';
import {TabContentComponent} from '../tab-content/tab-content.component';
import {Tab} from '../../interfaces/tab';
import {TabListService} from '../../services/tab-list.service';

@Component({
    selector: 'tabs',
    templateUrl: 'assets/components/tabs/tabs.html',
    styleUrls: ['assets/components/tabs/tabs.css'],
    directives: [
        TabContentComponent
    ]
})
export class TabsComponent
{
    @ViewChild('paperTabs')
    paperTabs;

    @ViewChild('ironPages')
    ironPages;

    private _counter = 0;

    constructor(private _tabListService: TabListService) {}

    selectTab(id: number) {
        this.paperTabs.nativeElement.select(id);
    }

    closeTab(tab: Tab) {
        this._tabListService.removeTab(tab);
    }

    getTabs(): Tab[] {
        return this._tabListService.getTabs();
    }

    newTab() {
        var tab = this._tabListService.addTab({
            "name": "Tab " + ++this._counter,
            "layoutName": "game"
        });
        this.selectTab(tab.id);
    }
}
