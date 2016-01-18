import {Injectable} from 'angular2/core';
import {Tab} from '../interfaces/tab';

@Injectable()
export class TabContentService
{
    getContent(tab: Tab): string {
        switch (tab.layout) {
            case 'greeter':
                return 'Hello World!';
            default:
                return 'There has been an internal error.';
        }
    }
}
