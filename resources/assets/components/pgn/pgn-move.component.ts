import {Component, Input} from 'angular2/core';

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
}
