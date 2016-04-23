import {Component, Input} from 'angular2/core';
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

    getMoves() {
        return this.line.moveHistory;
    }
}
