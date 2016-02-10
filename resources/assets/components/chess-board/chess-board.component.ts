import {ChangeDetectorRef, Component, ViewChild} from 'angular2/core';
import Chess from 'lib/chess-es6/src/chess';
import Flags from 'lib/chess-es6/src/flags';
import Move from 'lib/chess-es6/src/move';
import PieceType from 'lib/chess-es6/src/piece_type';
import Color from 'lib/chess-es6/src/color';
import Chessground from 'chessground';

@Component({
    selector: 'chess-board',
    templateUrl: 'assets/components/chess-board/chess-board.html',
    styleUrls: ['assets/components/chess-board/chess-board.css']
})
export class ChessBoardComponent
{
    @ViewChild('chessground')
    chessground;

    ground;
    chess;
    CHESS_COLOR_TO_GROUND_COLOR = {};
    CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE = {};

    chessgroundOptions = {
        movable: {
            free: false
        },
        events: {
            move: (orig, dest, capturedPiece) => { this.onBoardMove(orig, dest, capturedPiece) }
        }
    };

    constructor(private _cdRef: ChangeDetectorRef) {
        this.CHESS_COLOR_TO_GROUND_COLOR[Color.WHITE] = 'white';
        this.CHESS_COLOR_TO_GROUND_COLOR[Color.BLACK] = 'black';

        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.NONE] = null;
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.PAWN] = 'pawn';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.KNIGHT] = 'knight';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.BISHOP] = 'bishop';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.ROOK] = 'rook';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.QUEEN] = 'queen';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.KING] = 'king';

        this.chess = new Chess();
    }

    ngAfterViewInit() {
        this.ground = Chessground(this.chessground.nativeElement, this.chessgroundOptions);
        this._updateBoard();
        this.ground.dump().bounds.clear();
    }

    onBoardMove(orig, dest, capturedPiece) {
        var moveContext = this.chess.makeMoveFromAlgebraic(orig, dest);
        this._handleEnPassant(moveContext.move);
        this._handleCastling(moveContext.move);
        this._updateBoard();
        this._cdRef.detectChanges();
    }

    rotateBoard() {
        this.ground.toggleOrientation();
    }

    back() {
        this.chess.prev();
        this._updatePosition();
        this._updateBoard();
        this.ground.set({
            lastMove: null
        });
    }

    forward() {
        this.chess.next();
        this._updatePosition();
        this._updateBoard();
        this.ground.set({
            lastMove: null
        });
    }

    getScoresheet() {
        if (this.chess) {
            return this.chess.toPgn();
        } else {
            return "";
        }
    }


    private _updatePosition() {
        var array = {};
        for (var square in Move.SQUARES) {
            array[square] = this._chessPiece2groundPiece(this.chess.get(square));
        }
        this.ground.setPieces(array);
    }

    private _chessPiece2groundPiece(chessPiece) {
        if (chessPiece.type === PieceType.NONE) {
            return null;
        }
        return {
            color: this.CHESS_COLOR_TO_GROUND_COLOR[chessPiece.color],
            role: this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[chessPiece.type]
        };
    }

    private _getDests() {
        var dests = this.chess.moves({onlyAlgebraicSquares: true});
        var splitDests = dests.map((move) => { return move.split('-'); });
        var ret = {};
        var move;

        for (move of splitDests) {
            if (!ret[move[0]]) {
                ret[move[0]] = [move[1]];
            } else {
                ret[move[0]].push(move[1]);
            }
        }
        return ret;
    }

    private _getFullTurnColor() {
        if (this.chess.whoseTurn() == Color.WHITE) {
            return 'white';
        } else {
            return 'black';
        }
    }

    private _getFullNotTurnColor() {
        if (this.chess.whoseTurn() == Color.BLACK) {
            return 'white';
        } else {
            return 'black';
        }
    }


    private _whitesTurn(): boolean {
        return this.chess.whoseTurn() == 'w';
    }

    private _handleEnPassant(move) {
        // check if move is an en passant capture:
        if (move.flags & Flags.EP_CAPTURE) {
            var array = new Array();
            if (this._whitesTurn()) {
                array[this._squareToAlgebraic(move.to - 16)] = null;
            } else {
                array[this._squareToAlgebraic(move.to + 16)] = null;
            }
            this.ground.setPieces(array);
        }

    }

    private _handleCastling(move) {
        if (move.flags & Flags.KSIDE_CASTLE) {
            var array = new Array();
            array[this._squareToAlgebraic(move.to + 1)] = null;
            array[this._squareToAlgebraic(move.to - 1)] = {
                color: this._getFullNotTurnColor(),
                role: "rook"
            };
            this.ground.setPieces(array);
        }
        if (move.flags & Flags.QSIDE_CASTLE) {
            var array = new Array();
            array[this._squareToAlgebraic(move.to - 2)] = null;
            array[this._squareToAlgebraic(move.to + 1)] = {
                color: this._getFullNotTurnColor(),
                role: "rook"
            };
            this.ground.setPieces(array);
        }
    }

    private _squareToAlgebraic(square: number): string {
        return Move.SQUARES_LOOKUP[square];
    }

    private _updateBoard() {
        this.ground.set({
            turnColor: this._getFullTurnColor(),
            movable: {
                color: this._getFullTurnColor(),
                dests: this._getDests()
            }
        });
    }

}
