///<reference path="../../../typings/index.d.ts"/>
import {Injectable} from '@angular/core';
import {Tab} from '../interfaces/tab';

@Injectable()
export class TabListService
{
    private _tabs: Tab[] = [];

    private _counter = 0;

    getTabs(): Tab[] {
        return this._tabs;
    }

    addTab(tab: Tab): Tab {
        tab.id = this._counter++; // overwrite id to be the index of the tab in the array
        this._tabs.push(tab);
        return tab;
    }

    removeTab(tab: Tab) {
        var index: number = this._tabs.indexOf(tab);
        if (index > -1) {
            this._tabs.splice(index, 1);
        }
    }
}
