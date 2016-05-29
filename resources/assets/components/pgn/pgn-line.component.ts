///<reference path="../../../../typings/index.d.ts"/>
import {Component, Input, Output, EventEmitter} from '@angular/core';
import {PgnMoveComponent} from './pgn-move.component';

@Component({
    selector: 'pgn-line',
    templateUrl: 'assets/components/pgn/pgn-line.html',
    directives: [
        PgnMoveComponent,
        PgnLineComponent
    ]
})

export class PgnLineComponent
{
    @Input()
    line;

    @Input()
    basePosition;

    @Output()
    updatePosition: EventEmitter<any> = new EventEmitter();

    onUpdatePosition() {
        this.updatePosition.emit(null);
    }

    getMoves() {
        return this.line.moveHistory;
    }
}
