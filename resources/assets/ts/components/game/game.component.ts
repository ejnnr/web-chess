import {Component} from 'angular2/core';
import {ChessBoardComponent} from '../chess-board/chess-board.component';

@Component({
    selector: 'game',
    templateUrl: 'js/components/game/game.html',
    styleUrls: ['js/components/game/game.css'],
    directives: [
        ChessBoardComponent
    ]
})
export class GameComponent
{

}
