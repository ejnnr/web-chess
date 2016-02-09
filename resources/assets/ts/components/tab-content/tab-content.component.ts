import {Component, Input, OnInit, DynamicComponentLoader, ElementRef} from 'angular2/core';
import {Tab} from '../../interfaces/tab';
import {TabContentService} from '../../services/tab-content.service';
import {ChessBoardComponent} from '../chess-board/chess-board.component';

@Component({
    selector: 'tab-content',
    templateUrl: 'js/components/tab-content/tab-content.html',
    styleUrls: ['js/components/tab-content/tab-content.css']
})
export class TabContentComponent implements OnInit
{
    @Input()
    tab: Tab;

    constructor(private _tabContentService: TabContentService,
                private _dcl: DynamicComponentLoader,
                private _elRef: ElementRef) {}

    ngOnInit() {
        this._dcl.loadIntoLocation(this.getComponent(), this._elRef, 'component');
    }

    getComponent() {
        return this._tabContentService.getLayout(this.tab).component;
    }
}
