import {Injectable} from 'angular2/core';
import {Tab} from '../interfaces/tab';

@Injectable()
export class TabListService
{
    private _tabs: Tab[] = [];

    getTabs(): Tab[] {
        return this._tabs;
    }

    addTab(tab: Tab) {
        tab.id = this._tabs.length; // overwrite id to be the index of the tab in the array
        this._tabs.push(tab);
    }

    removeTab(tab: Tab) {
        var index: number = this._tabs.indexOf(tab);
        if (index > -1) {
            this._tabs.splice(index, 1);
        }
    }
}
