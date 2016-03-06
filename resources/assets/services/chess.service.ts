import {Injectable} from 'angular2/core';
import Chess from 'lib/chess-es6/src/chess';
import Flags from 'lib/chess-es6/src/flags';
import Move from 'lib/chess-es6/src/move';
import PieceType from 'lib/chess-es6/src/piece_type';
import Piece from 'lib/chess-es6/src/piece';
import Color from 'lib/chess-es6/src/color';

@Injectable()
export class ChessService
{
    chess;

    CHESS_COLOR_TO_GROUND_COLOR = {};
    CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE = {};
    GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE = {};

    constructor() {
        this.CHESS_COLOR_TO_GROUND_COLOR[Color.WHITE] = 'white';
        this.CHESS_COLOR_TO_GROUND_COLOR[Color.BLACK] = 'black';

        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.NONE] = null;
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.PAWN] = 'pawn';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.KNIGHT] = 'knight';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.BISHOP] = 'bishop';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.ROOK] = 'rook';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.QUEEN] = 'queen';
        this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[PieceType.KING] = 'king';

        this.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE['pawn'] = PieceType.PAWN;
        this.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE['knight'] = PieceType.KNIGHT;
        this.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE['bishop'] = PieceType.BISHOP;
        this.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE['rook'] = PieceType.ROOK;
        this.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE['queen'] = PieceType.QUEEN;
        this.GROUND_PIECE_TYPE_TO_CHESS_PIECE_TYPE['king'] = PieceType.KING;

        this.chess = new Chess();
    }

    squareToAlgebraic(square: number): string {
        return Move.SQUARES_LOOKUP[square];
    }

    chessPiece2groundPiece(chessPiece) {
        if (chessPiece.type === PieceType.NONE) {
            return null;
        }
        return {
            color: this.CHESS_COLOR_TO_GROUND_COLOR[chessPiece.color],
            role: this.CHESS_PIECE_TYPE_TO_GROUND_PIECE_TYPE[chessPiece.type]
        };
    }

    getDests() {
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

    getFullTurnColor() {
        if (this.chess.whoseTurn() == Color.WHITE) {
            return 'white';
        } else {
            return 'black';
        }
    }

    getFullNotTurnColor() {
        if (this.chess.whoseTurn() == Color.BLACK) {
            return 'white';
        } else {
            return 'black';
        }
    }

    whitesTurn(): boolean {
        return this.chess.whoseTurn() == 'w';
    }
}
