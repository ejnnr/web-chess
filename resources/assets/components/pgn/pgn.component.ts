///<reference path="../../../../typings/index.d.ts"/>
import {Component, Input, Output, EventEmitter} from '@angular/core';
import {PgnLineComponent} from './pgn-line.component';
import {ChessService} from '../../services/chess.service';

@Component({
    selector: 'pgn',
    templateUrl: 'assets/components/pgn/pgn.html',
    directives: [
        PgnLineComponent
    ]
})

export class PgnComponent
{
    @Output()
    updatePosition: EventEmitter<any> = new EventEmitter();

    constructor(private _chessService: ChessService) {
    }

    onUpdatePosition() {
        this.updatePosition.emit(null);
    }

    getMainline() {
        return this._chessService.chess.currentGame.boardVariations[0];
    }

    getBasePosition() {
        return [];
    }
}
