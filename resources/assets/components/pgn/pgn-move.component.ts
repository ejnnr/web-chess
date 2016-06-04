///<reference path="../../../../typings/index.d.ts"/>
import {Component, Output, Input, EventEmitter} from '@angular/core';
import Color from 'chess-es6.js/color';
import {ChessService} from '../../services/chess.service';

@Component({
    selector: 'pgn-move',
    templateUrl: 'assets/components/pgn/pgn-move.html',
    styleUrls: ['assets/components/pgn/pgn-move.css'],
    directives: [
    ]
})

export class PgnMoveComponent
{
    @Input()
    moveContext;

    @Input()
    positionIndex;

    @Output()
    updatePosition: EventEmitter<any> = new EventEmitter();

    constructor(private _chessService: ChessService) {
    }

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

    goToPosition() {
        this._chessService.chess.currentGame.goToPosition(this.positionIndex);
        this.updatePosition.emit(null);
    }
}
