import {Injectable} from 'angular2/core';
import {Tab} from '../interfaces/tab';
import {Layout} from '../interfaces/layout';
import {allLayouts} from '../layouts/list';

@Injectable()
export class TabContentService
{
    getLayout(tab: Tab): Layout {
        if (tab.layoutName in allLayouts) {
            return allLayouts[tab.layoutName];
        } else {
            throw "unrecognized layout name: " + tab.layoutName;
        }
    }
}
