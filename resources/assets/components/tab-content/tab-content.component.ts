///<reference path="../../../../typings/index.d.ts"/>
import {Component, Input, OnInit, ViewChild, ComponentResolver, ViewContainerRef} from '@angular/core';
import {Tab} from '../../interfaces/tab';
import {TabContentService} from '../../services/tab-content.service';
import {ChessBoardComponent} from '../chess-board/chess-board.component';

@Component({
    selector: 'tab-content',
    templateUrl: 'assets/components/tab-content/tab-content.html',
    styleUrls: ['assets/components/tab-content/tab-content.css']
})
export class TabContentComponent implements OnInit
{
    @Input()
    tab: Tab;

    @ViewChild("componentAnchor", { read: ViewContainerRef })
    childContainer: ViewContainerRef;

    constructor(private _tabContentService: TabContentService,
                private _cResolver: ComponentResolver) {}

    ngOnInit() {
        this._cResolver.resolveComponent(this.getComponent()).then(componentFactory => this.childContainer.createComponent(componentFactory));
    }

    getComponent() {
        return this._tabContentService.getLayout(this.tab).component;
    }
}
