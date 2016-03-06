import {Component, Input} from 'angular2/core';
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
    constructor(private _chessService: ChessService) {
    }

    getMainline() {
        return this._chessService.chess.currentGame.boardVariations[0];
    }
}
