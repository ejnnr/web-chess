import {Component} from 'angular2/core';
import {ChessBoardComponent} from '../chess-board/chess-board.component';
import {PgnComponent} from '../pgn/pgn.component';
import {ChessService} from '../../services/chess.service';

@Component({
    selector: 'game',
    templateUrl: 'assets/components/game/game.html',
    styleUrls: ['assets/components/game/game.css'],
    directives: [
        ChessBoardComponent,
        PgnComponent
    ],
    providers: [
        ChessService
    ]
})
export class GameComponent
{

}
