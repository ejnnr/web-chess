import {Component, Input} from 'angular2/core';
import {Tab} from '../../interfaces/tab';
import {TabContentService} from '../../services/tab-content.service';

@Component({
    selector: 'tab-content',
    templateUrl: 'js/components/tab-content/tab-content.html'
})
export class TabContentComponent
{
    @Input()
    tab: Tab;

    constructor(private _tabContentService: TabContentService) {}

    getContent() {
        return this._tabContentService.getContent(this.tab);
    }
}
