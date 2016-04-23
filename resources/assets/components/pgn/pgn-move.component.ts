import {Component, Input} from 'angular2/core';
import Color from 'lib/chess-es6/src/color';

@Component({
    selector: 'pgn-move',
    templateUrl: 'assets/components/pgn/pgn-move.html',
    directives: [
    ]
})

export class PgnMoveComponent
{
    @Input()
    moveContext;

    getSAN(): string {
        return this.moveContext.move.san;
    }

    getMoveNumber(): number {
        return this.moveContext.moveNumber;
    }

    getMoveNumberString(): string {
        if (this.moveContext.turn === Color.WHITE) {
            return this.getMoveNumber() + '. ';
        }

        return '';
    }
}
