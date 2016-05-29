///<reference path="../../../../typings/index.d.ts"/>
import {ChangeDetectorRef, Component, ViewChild} from '@angular/core';
import {ChessService} from '../../services/chess.service';
import Chess from 'lib/chess-es6/src/chess';
import Flags from 'lib/chess-es6/src/flags';
import Move from 'lib/chess-es6/src/move';
import PieceType from 'lib/chess-es6/src/piece_type';
import Piece from 'lib/chess-es6/src/piece';
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

    private _begunPromotion = false;

    chessgroundOptions = {
        movable: {
            free: false
        },
        events: {
            move: (orig, dest, capturedPiece) => { this.onBoardMove(orig, dest, capturedPiece) }
        }
    };

    constructor(private _cdRef: ChangeDetectorRef, private _chessService: ChessService) {
    }

    ngAfterViewInit() {
        this.ground = Chessground(this.chessground.nativeElement, this.chessgroundOptions);
        this._updateBoard();
        this.ground.dump().bounds.clear();
    }

    update() {
        this._updatePosition();
        this._updateBoard();
        this.ground.set({
            lastMove: null
        });
    }

    onBoardMove(orig, dest, capturedPiece) {
        this._begunPromotion = false;
        var moveContext = this._chessService.chess.makeMoveFromAlgebraic(orig, dest);
        this._handleEnPassant(moveContext.move);
        this._handleCastling(moveContext.move);
        this._handlePromotion(moveContext.move);
        this._updateBoard();
        this._cdRef.detectChanges();
    }

    rotateBoard() {
        this.ground.toggleOrientation();
    }

    back() {
        this._chessService.chess.prev();
        this._updatePosition();
        this._updateBoard();
        this.ground.set({
            lastMove: null
        });
    }

    forward() {
        this._chessService.chess.next();
        this._updatePosition();
        this._updateBoard();
        this.ground.set({
            lastMove: null
        });
    }

    getScoresheet() {
        if (this._chessService.chess) {
            return this._chessService.chess.toPgn();
        } else {
            return "";
        }
    }

    private _updatePosition() {
        var array = {};
        for (var square in Move.SQUARES) {
            array[square] = this._chessService.chessPiece2groundPiece(this._chessService.chess.get(square));
        }
        this.ground.setPieces(array);
    }

    private _handleEnPassant(move) {
        // check if move is an en passant capture:
        if (move.flags & Flags.EP_CAPTURE) {
            var array = new Array();
            if (this._chessService.whitesTurn()) {
                array[this._chessService.squareToAlgebraic(move.to - 16)] = null;
            } else {
                array[this._chessService.squareToAlgebraic(move.to + 16)] = null;
            }
            this.ground.setPieces(array);
        }

    }

    private _handlePromotion(move) {
        // check if move is a promotion:
        if (move.flags & Flags.PROMOTION) {
            var array = {};
            this._begunPromotion = true; // to render the promotion choice visible
            array[this._chessService.squareToAlgebraic(move.to)] = {
                color: this._chessService.getFullNotTurnColor(),
                role: "queen" // queen is default
            };
            this.ground.setPieces(array);
        }
    }

    private _setPromotion(pieceType) {
        pieceType = this._chessService.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE[pieceType];
        if (pieceType === PieceType.QUEEN) {
            this._begunPromotion = false;
            // queen is the default and has already been used automatically
            return;
        }
        
        var oldMove = this._chessService.chess.currentGame.currentVariation.undoCurrentMove();

        // redo the altered move:
        this._chessService.chess.makeMoveFromAlgebraic(this._chessService.squareToAlgebraic(oldMove.from), this._chessService.squareToAlgebraic(oldMove.to), pieceType);

        var array = {};
        array[this._chessService.squareToAlgebraic(oldMove.to)] = {
            color: this._chessService.getFullNotTurnColor(),
            role: this._chessService.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[pieceType]
        };
        this.ground.setPieces(array);

        // update legal moves (might have changed because the new piece gives check/...
        this._updateBoard();

        this._begunPromotion = false;
    }

    private _handleCastling(move) {
        if (move.flags & Flags.KSIDE_CASTLE) {
            var array = new Array();
            array[this._chessService.squareToAlgebraic(move.to + 1)] = null;
            array[this._chessService.squareToAlgebraic(move.to - 1)] = {
                color: this._chessService.getFullNotTurnColor(),
                role: "rook"
            };
            this.ground.setPieces(array);
        }
        if (move.flags & Flags.QSIDE_CASTLE) {
            var array = new Array();
            array[this._chessService.squareToAlgebraic(move.to - 2)] = null;
            array[this._chessService.squareToAlgebraic(move.to + 1)] = {
                color: this._chessService.getFullNotTurnColor(),
                role: "rook"
            };
            this.ground.setPieces(array);
        }
    }

    private _updateBoard() {
        this.ground.set({
            turnColor: this._chessService.getFullTurnColor(),
            movable: {
                color: this._chessService.getFullTurnColor(),
                dests: this._chessService.getDests()
            }
        });
    }

}
